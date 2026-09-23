FROM node:22-alpine AS development
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
EXPOSE 9000
CMD ["npm", "run", "dev"]
