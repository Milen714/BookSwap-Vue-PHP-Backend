const WebSocket = require('ws');
const { createClient } = require('redis');
const jwt = require('jsonwebtoken');
const url = require('url');

const JWT_SECRET = process.env.JWT_SECRET_KEY || 'default_secret_key';
const PORT = process.env.WS_INTERNAL_PORT || 8080;
const wss = new WebSocket.Server({ port: PORT });

// Connect to Redis using the environment variable we set in docker-compose.yml
const redisClient = createClient({
    url: process.env.REDIS_URL || 'redis://redis:6379'
});

redisClient.on('error', (err) => console.log('Redis Client Error', err));

async function start() {
    await redisClient.connect();
    console.log('Node.js successfully connected to Redis!');

    const subscriber = redisClient.duplicate();
    await subscriber.connect();

    // Listen for the channel your PHP app publishes to
    await subscriber.subscribe('chat-channel', (message) => {
        console.log("Broadcasting message from PHP (chat-channel):", message);
        
        wss.clients.forEach((client) => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(message);
            }
        });
    });

    // Optionally subscribe to other channels (e.g., notifications)
}

start();

wss.on('connection', (ws, request) => {
    const parameters = url.parse(request.url, true).query;
    const token = parameters.token;

    if (!token){
        console.log('Connection rejected: No token');
        ws.close(4001, 'Authentication token required');
        return;
    }

    try {
        console.log('Attempting to verify token with secret:', JWT_SECRET);
        console.log('Token received:', token.substring(0, 50) + '...');
        
        const decoded = jwt.verify(token, JWT_SECRET);
        console.log('Token verified successfully. User data:', decoded.data);
        ws.userId = decoded.data.id;
    } catch (error) {
        console.log('Connection rejected: Invalid token');
        console.log('Verification error:', error.message);
        ws.close(4001, 'Invalid authentication token');
        return;
    }

    // 2. Tag this specific socket connection with their ID
    if (parameters.userId) {
        ws.userId = parseInt(parameters.userId);
        console.log(`User ${ws.userId} connected to the server.`);
        
        // Broadcast to all other clients that this user connected
        wss.clients.forEach((client) => {
            if (client !== ws && client.readyState === WebSocket.OPEN) {
                client.send(JSON.stringify({
                    type: 'user-connected',
                    userId: ws.userId
                }));
            }
        });
    } else {
        console.log('An anonymous user connected.');
    }
    const ip = request.socket.remoteAddress;
    console.log(`✅ Client connected from ${ip}`);

    ws.on('message', (rawData) => {
        const message = rawData.toString();
        console.log(`📨 Received message from ${ip}:`, message);
        
        // Echo back to the sender
        ws.send(`Server received: ${message}`);
        
        //Optionally broadcast to all clients
        wss.clients.forEach((client) => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(`Broadcast from ${ip}: ${message}`);
            }
        });
    });

    ws.on('error', (err) => {
        console.error(`❌ WebSocket error from ${ip}:`, err.message);
    });

    ws.on('close', () => {
        console.log(`⚠️ Client from ${ip} disconnected`);
        
        // Broadcast disconnection if user had an ID
        if (ws.userId) {
            wss.clients.forEach((client) => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({
                        type: 'user-disconnected',
                        userId: ws.userId
                    }));
                }
            });
        }
    });
});

console.log(`WebSocket server is running on port ${PORT}`);