# Etapa 1: Construcción (Build) con Node.js 20
FROM node:20-alpine AS build-stage

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

# Compilar la SPA para producción
RUN npm run build

# Etapa 2: Producción con Nginx Alpine
FROM nginx:alpine AS production-stage

# Copiar la configuración personalizada de Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copiar los archivos estáticos compilados desde la etapa de construcción
COPY --from=build-stage /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
