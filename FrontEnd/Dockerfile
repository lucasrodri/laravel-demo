# Fetching the latest node image on alpine linux
FROM node:alpine AS development

# Declaring env
ENV NODE_ENV development

# Setting up the work directory
WORKDIR /react-app

# Installing dependencies
#COPY ./app/package.json /react-app
#RUN npm install

# Copying all the files in our project
#Nao vai precisar pois eu faço o bind no docker-compose
#COPY ./app .

# Starting our application
CMD ["npm", "start"]