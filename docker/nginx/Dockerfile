FROM nginx:1.17.8-alpine

# Copy the public directory
COPY . /app

# Copy the nginx config file
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
