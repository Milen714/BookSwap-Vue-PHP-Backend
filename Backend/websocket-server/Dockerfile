FROM node:18-alpine
WORKDIR /websocket-server
COPY package*.json ./
RUN npm install
COPY . .
EXPOSE 6001
CMD ["node", "server.js"]