# ==============================================================================
# Viewfinder Upstream - Container Image
# ==============================================================================
# A streamlined Digital Sovereignty Readiness Assessment tool
# Based on Red Hat Universal Base Image 9 with PHP 8.3
#
# Features:
# - 21 questions across 7 critical domains
# - 4-level maturity assessment (Foundation, Developing, Strategic, Advanced)
# - PDF report generation
# - Progress auto-save
# ==============================================================================

FROM registry.access.redhat.com/ubi9/php-83:latest

# ------------------------------------------------------------------------------
# Metadata
# ------------------------------------------------------------------------------
LABEL maintainer="Chris Jenkins <chrisj@redhat.com>" \
      name="viewfinder-upstream" \
      version="1.0.0" \
      description="Viewfinder Upstream - Digital Sovereignty Readiness Assessment" \
      summary="Lightweight assessment tool for evaluating digital sovereignty posture across 7 domains" \
      io.k8s.description="Digital Sovereignty Readiness Assessment tool with 7 domain coverage and 4-level maturity model" \
      io.k8s.display-name="Viewfinder Upstream" \
      io.openshift.tags="assessment,digital-sovereignty,php,redhat"

# ------------------------------------------------------------------------------
# Environment Setup
# ------------------------------------------------------------------------------
ENV APP_ROOT=/opt/app-root/src \
    PHP_VERSION=8.3 \
    PATH=/opt/app-root/src/bin:/opt/app-root/bin:$PATH

WORKDIR ${APP_ROOT}

# ------------------------------------------------------------------------------
# System Dependencies
# ------------------------------------------------------------------------------
USER root

# Install required packages and clean up in single layer
RUN dnf install -y \
        httpd \
        php-fpm \
        php-json \
        php-gd \
        php-mbstring \
    && dnf clean all \
    && rm -rf /var/cache/dnf

# ------------------------------------------------------------------------------
# Apache Security Configuration
# ------------------------------------------------------------------------------
RUN sed -i 's/^ServerTokens .*/ServerTokens Prod/' /etc/httpd/conf/httpd.conf && \
    sed -i 's/^ServerSignature .*/ServerSignature Off/' /etc/httpd/conf/httpd.conf && \
    echo 'Header always set X-Content-Type-Options "nosniff"' >> /etc/httpd/conf/httpd.conf && \
    echo 'Header always set X-Frame-Options "SAMEORIGIN"' >> /etc/httpd/conf/httpd.conf && \
    echo 'Header always set X-XSS-Protection "1; mode=block"' >> /etc/httpd/conf/httpd.conf && \
    echo 'Header always set Referrer-Policy "strict-origin-when-cross-origin"' >> /etc/httpd/conf/httpd.conf

# ------------------------------------------------------------------------------
# Composer Installation
# ------------------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer && \
    chmod +x /usr/local/bin/composer

# ------------------------------------------------------------------------------
# PHP Dependencies
# ------------------------------------------------------------------------------
# Copy composer files first for better Docker layer caching
COPY --chown=1001:0 composer.json composer.lock* ./

# Install dependencies with production optimizations
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
        --no-progress \
    && composer clear-cache

# ------------------------------------------------------------------------------
# Application Files
# ------------------------------------------------------------------------------
# Copy application code
COPY --chown=1001:0 index.php ./
COPY --chown=1001:0 includes/ ./includes/
COPY --chown=1001:0 ds-qualifier/ ./ds-qualifier/
COPY --chown=1001:0 error-pages/ ./error-pages/
COPY --chown=1001:0 css/ ./css/
#COPY --chown=1001:0 js/ ./js/
COPY --chown=1001:0 images/ ./images/
COPY --chown=1001:0 README.md ./

# ------------------------------------------------------------------------------
# Directory Structure & Permissions
# ------------------------------------------------------------------------------
# Create required directories
RUN mkdir -p ${APP_ROOT}/logs

# Set ownership and permissions for OpenShift compatibility
# Files: 644, Directories: 755, Group writable: logs
RUN chown -R 1001:0 ${APP_ROOT} && \
    chmod -R g=u ${APP_ROOT} && \
    find ${APP_ROOT} -type d -exec chmod 755 {} \; && \
    find ${APP_ROOT} -type f -exec chmod 644 {} \; && \
    chmod 775 ${APP_ROOT}/logs

# ------------------------------------------------------------------------------
# Runtime Configuration
# ------------------------------------------------------------------------------
# Switch to non-root user for security
USER 1001

# Health check - verify application is responding
HEALTHCHECK --interval=30s \
            --timeout=5s \
            --start-period=10s \
            --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Expose application port
EXPOSE 8080

# ------------------------------------------------------------------------------
# Container Startup
# ------------------------------------------------------------------------------
# Use PHP built-in development server
# Note: For production, consider using Apache/Nginx + PHP-FPM
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/opt/app-root/src"]

# ==============================================================================
# Build Instructions:
# ------------------
# Build: podman build -t viewfinder-upstream:latest .
# Run:   podman run -d -p 8080:8080 --name viewfinder-upstream viewfinder-upstream:latest
# ==============================================================================
