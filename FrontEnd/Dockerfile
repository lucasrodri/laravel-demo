# Fetching the latest node image on alpine linux
FROM node:alpine AS development

# Declaring env
ENV NODE_ENV development

# Setting up the work directory
WORKDIR /app

#Copy the run.sh file
COPY run.sh /usr/bin/run.sh

# Installing dependencies
#Nao vai precisar pois eu faço o bind no docker-compose
#COPY ./app/package.json ./
#RUN npm install

# Copying all the files in our project
#Nao vai precisar pois eu faço o bind no docker-compose
#COPY ./app .

RUN chmod +x /usr/bin/run.sh

EXPOSE 3000

# Starting our application
CMD ["/usr/bin/run.sh"]