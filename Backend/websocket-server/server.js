const WebSocket = require('ws');
const { createClient } = require('redis');
const url = require('url');

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

    // Also subscribe to book-search channel
    await subscriber.subscribe('book-search', (message) => {
        console.log("Broadcasting message from PHP (book-search):", message);
        
        wss.clients.forEach((client) => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(message);
            }
        });
    });
}

start();

wss.on('connection', (ws, request) => {
    const parameters = url.parse(request.url, true).query;
    
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