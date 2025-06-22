# Build React
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Runtime Nginx
FROM nginx:alpine
COPY --from=build /app/build /usr/share/nginx/html
RUN printf "server { \
    listen 80; \
    root /usr/share/nginx/html; \
    location / { try_files \$uri /index.html; } \
}" > /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx","-g","daemon off;"]
