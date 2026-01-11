FROM mcr.microsoft.com/playwright:v1.57.0-jammy

WORKDIR /app

COPY package.json ./

RUN npm install

COPY . .

CMD ["node", "src/index.js"]
