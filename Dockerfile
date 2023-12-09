# Use the existing base image
FROM php:8.0-apache

# Set metadata for the image
LABEL maintainer="Nemeth Balint"

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    zip \
    unzip \
    git

# Install PHP extensions using PECL
RUN pecl install zip && \
    docker-php-ext-enable zip


# Download and install Arduino CLI
RUN curl -fsSL https://raw.githubusercontent.com/arduino/arduino-cli/master/install.sh | BINDIR=/usr/bin sh

WORKDIR /var/www/html/

# Initialize Arduino CLI amnd LIBS
RUN mkdir arduino

RUN arduino-cli config init --dest-file /var/www/html/arduino/config.yaml

RUN arduino-cli --config-file /var/www/html/arduino/config.yaml config set directories.data /var/www/html/arduino/data
RUN arduino-cli --config-file /var/www/html/arduino/config.yaml config set directories.downloads /var/www/html/arduino/downloads
RUN arduino-cli --config-file /var/www/html/arduino/config.yaml config set directories.user /var/www/html/arduino/user

RUN arduino-cli --config-file /var/www/html/arduino/config.yaml config set library.enable_unsafe_install true

RUN arduino-cli --config-file /var/www/html/arduino/config.yaml core update-index

RUN arduino-cli --config-file /var/www/html/arduino/config.yaml core install arduino:avr

RUN arduino-cli --config-file /var/www/html/arduino/config.yaml lib install --git-url https://github.com/Aranyalma2/plcFramework.git

# Download and copy Code translator

RUN git clone https://github.com/Aranyalma2/plcCompiler.git

# Copy the contents of the "src" folder to the web server root
RUN cp -R plcCompiler/src/* /var/www/html/

#Remove all unnecessary files and folders
RUN rm -fr plcCompiler


# Cleanup: Remove unnecessary files or tools (e.g., git) if desired
RUN apt-get remove -y git && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/*

# Disable serving content from arduino and libraryDatas
RUN echo "DocumentRoot /var/www/html" > /etc/apache2/sites-available/000-default.conf && \
    echo "<Directory /var/www/html/arduino>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Options None" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    AllowOverride None" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Require all denied" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "<Directory /var/www/html/libraryDatas>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Options None" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    AllowOverride None" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Require all denied" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf


# Expose port 80 for Apache
EXPOSE 80


# Start Apache
CMD ["apache2-foreground"]
