FROM debian:jessie
 
MAINTAINER Mathieu Thomas <mthomas@blahp.fr>
 
RUN apt-get update && apt-get install -y \
    nginx
 
ADD nginx.conf /etc/nginx/
ADD starting5.conf /etc/nginx/sites-available/
 
RUN ln -s /etc/nginx/sites-available/starting5.conf /etc/nginx/sites-enabled/starting5
RUN rm /etc/nginx/sites-enabled/default
 
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf
 
RUN usermod -u 501 www-data
 
CMD ["nginx"]
 
EXPOSE 80
EXPOSE 443
