# Digital Sovereignty Readiness Assessment

> **Upstream, Open Source Version**
>
> This is the upstream, open source version of the Red Hat Digital Sovereignty Readiness Assessment tool. We encourage collaboration, contributions, and community feedback to help organizations worldwide evaluate and improve their digital sovereignty posture.

## Overview

This streamlined assessment tool helps organizations evaluate their digital sovereignty readiness across 7 critical domains in just 10-15 minutes. The tool provides:

- **Quick Assessment**: 21 targeted questions designed for rapid evaluation
- **4-Level Maturity Model**: Foundation, Developing, Strategic, and Advanced levels
- **Actionable Insights**: Specific recommendations based on your maturity level
- **PDF Reports**: Downloadable reports for stakeholders
- **Open Source**: Community-driven development and transparency

## About This Project

This is an **upstream open source project** maintained by the Red Hat Community of Practice (CoP). The tool is designed to be:

- **Freely Available**: Open source under Apache 2.0 license
- **Community-Driven**: Contributions welcome from organizations and individuals
- **Vendor-Neutral**: Applicable to any organization, regardless of technology stack
- **Privacy-Focused**: No data collection - all assessment data stays in your browser

## Screenshots

### Landing Page
The landing page features the Digital Sovereignty Readiness Assessment.

![Landing Page - Balanced Profile](images/screenshots/landing-page-balanced.png)

### Assessment Page
The assessment questionnaire presents 21 questions across 7 domains with Yes/No/"Don't Know" response options. Progress is auto-saved to browser storage.

![Assessment Page](images/screenshots/assessment-page.png)

### Results Pages
Comprehensive results display showing scoring, maturity level, domain analysis, and actionable recommendations.

![Results Page - Overview](images/screenshots/results-page1.png)

![Results Page - Domain Analysis](images/screenshots/results-page2.png)

![Results Page - Recommendations](images/screenshots/results-page3.png)

### PDF Report
Professional PDF report with scores, domain breakdown, maturity level assessment, and tailored improvement actions.

![PDF Report Sample](images/screenshots/pdf-report-sample.png)

## Features

### Digital Sovereignty Readiness Assessment
- **Quick Assessment**: Complete evaluation in 10-15 minutes
- **7 Critical Domains**: Comprehensive coverage across:
  - Data Sovereignty
  - Technical Sovereignty
  - Operational Sovereignty
  - Assurance Sovereignty
  - Open Source Strategy
  - Executive Oversight
  - Managed Services
- **21 Key Questions**: 2-3 targeted questions per domain
- **Multiple Response Options**: Yes/No/"Don't Know" format
- **Instant Scoring**: Real-time maturity level calculation
- **Maturity Levels**: Foundation, Developing, Strategic, Advanced
- **Actionable Recommendations**: Tailored guidance based on assessment results
- **Research Questions**: Track "Don't Know" responses for follow-up investigation
- **PDF Export**: Professional downloadable reports
- **Progress Auto-Save**: Browser-based session persistence
- **Keyboard Navigation**: Arrow keys for quick navigation, Ctrl+S to save

## Installation

### Prerequisites
- PHP 8.1 or higher
- Apache or Nginx web server
- Composer (for dependency management)

### Local Installation

1. **Clone or extract the application**:
   ```bash
   cd /var/www/html/viewfinder-lite
   ```

2. **Install dependencies**:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Set file permissions**:
   ```bash
   # Set ownership (adjust user/group for your system)
   sudo chown -R apache:apache /var/www/html/viewfinder-lite

   # Set directory permissions
   sudo chmod 755 /var/www/html/viewfinder-lite
   sudo chmod 775 /var/www/html/viewfinder-lite/logs

   # Set file permissions
   find /var/www/html/viewfinder-lite -type f -exec chmod 644 {} \;
   ```

4. **Configure web server**:
   - See [Web Server Configuration](#web-server-configuration) below

5. **Access the application**:
   ```
   http://your-server/viewfinder-lite
   ```

### Podman Installation

1. **Build the container**:
   ```bash
   cd /var/www/html/viewfinder-lite
   podman build -t viewfinder-lite:latest .
   ```

2. **Run the container**:
   ```bash
   podman run -d -p 8080:8080 --name viewfinder-lite viewfinder-lite:latest
   ```

3. **Access the application**:
   ```
   http://localhost:8080
   ```

## Web Server Configuration

### Apache Configuration

**VirtualHost Example** (`/etc/httpd/conf.d/viewfinder-lite.conf`):
```apache
<VirtualHost *:80>
    ServerName viewfinder-lite.example.com
    DocumentRoot /var/www/html/viewfinder-lite

    <Directory /var/www/html/viewfinder-lite>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Security headers
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "SAMEORIGIN"
        Header always set X-XSS-Protection "1; mode=block"
    </Directory>

    # Logging
    ErrorLog /var/log/httpd/viewfinder-lite-error.log
    CustomLog /var/log/httpd/viewfinder-lite-access.log combined
</VirtualHost>
```

### Nginx Configuration

**Server Block Example** (`/etc/nginx/conf.d/viewfinder-lite.conf`):
```nginx
server {
    listen 80;
    server_name viewfinder-lite.example.com;
    root /var/www/html/viewfinder-lite;
    index index.php;

    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    # Logging
    access_log /var/log/nginx/viewfinder-lite-access.log;
    error_log /var/log/nginx/viewfinder-lite-error.log;
}
```

## File Structure

```
viewfinder-lite/
├── index.php                    # Landing page
├── composer.json                # PHP dependencies
├── composer.lock                # Dependency lock file
├── Dockerfile                   # Container build configuration
├── README.md                    # This file
├── CHANGES.md                   # Change log
├── IMPLEMENTATION_SUMMARY.txt   # Implementation details
│
├── ds-qualifier/                # Digital Sovereignty Readiness Assessment
│   ├── index.php               # Assessment questionnaire interface
│   ├── results.php             # Results and recommendations page
│   ├── config.php              # Questions configuration
│   ├── generate-pdf.php        # PDF report generator
│   ├── css/
│   │   └── ds-qualifier.css    # Assessment-specific styles
│   └── js/
│       └── ds-qualifier.js     # Interactive features & auto-save
│
├── includes/                    # Core backend classes
│   ├── Config.php              # Application configuration
│   ├── Security.php            # Security utilities
│   ├── Logger.php              # Logging functionality
│   └── Exceptions/             # Custom exception classes
│       ├── ViewfinderException.php
│       ├── FileSystemException.php
│       ├── DataValidationException.php
│       ├── ConfigurationException.php
│       └── ViewfinderJsonException.php
│
├── css/                         # Shared stylesheets
│   ├── bootstrap.min.css       # Bootstrap framework
│   ├── brands.css              # Font Awesome brands
│   ├── style.css               # Main application styles
│   ├── tab-dark.css            # Dark theme tab styling
│   ├── patternfly.css          # Red Hat PatternFly design system
│   └── patternfly-addons.css   # PatternFly extensions
│
├── js/                          # Shared JavaScript files
│
├── images/                      # Images and logos
│   └── screenshots/             # Documentation screenshots
│       ├── landing-page-balanced.png
│       ├── assessment-page.png
│       ├── results-page1.png
│       ├── results-page2.png
│       ├── results-page3.png
│       └── pdf-report-sample.png
│
├── error-pages/                 # Error handling pages
│   └── error-handler.php
│
├── logs/                        # Application logs (created at runtime)
│
└── vendor/                      # Composer dependencies (created by composer install)
```

## Usage

### Landing Page
Navigate to the root URL to access the landing page featuring the Digital Sovereignty Readiness Assessment card.

### Taking an Assessment

1. **Start Assessment**: Click "Start Assessment" button to begin
2. **Answer Questions**: Progress through 7 domains
   - Use Next/Previous buttons to navigate
   - Answer Yes/No or select "Don't Know" for uncertain items
   - Questions are validated before proceeding
   - Progress auto-saves to browser storage
3. **Submit**: Click "Complete Assessment" on the final section
4. **View Results**: Review your maturity level and recommendations
5. **Download Report**: Generate PDF report for stakeholders
6. **Take New Assessment**: Start fresh assessment anytime

### Understanding Results

#### Maturity Levels

Based on your score (0-21 points):

- **Foundation (0-5 points)**: Early-stage maturity
  - Ad-hoc processes with minimal sovereignty controls
  - Significant dependencies on external providers
  - Focus: Establish executive awareness and basic policies

- **Developing (6-10 points)**: Growing maturity
  - Basic controls are in place but not yet standardized
  - Projects are planned but processes may not be repeatable organization-wide
  - Focus: Build repeatable practices and implement foundational controls

- **Strategic (11-16 points)**: Mature posture
  - Processes are well characterized, understood, documented, and standardized
  - Digital sovereignty practices are consistent and repeatable across the organization
  - Clear governance structures and policies are in place
  - Focus: Ensure organization-wide consistency and pursue certifications

- **Advanced (17-21 points)**: Leading maturity
  - Continuous improvement through quantitative feedback and innovation
  - Proactive identification and deployment of innovative sovereignty practices
  - Industry-leading posture with thought leadership contributions
  - Focus: Drive innovation and lead industry best practices

#### Results Components

- **Score Breakdown**: Percentage-based maturity indicator
- **Domain Analysis Table**: Shows score and maturity level per domain
  - Progress bars show percentage completion per domain
- **Improvement Actions**: Recommended next steps based on maturity level
- **Domain Insights**: Detailed view of strengths and improvement areas
- **Research Questions**: "Don't Know" responses flagged for further investigation

## Configuration

### Application Settings
Edit `includes/Config.php` to modify:
- Application name and version
- Base paths
- Error handling settings
- Security configuration

### Assessment Questions
Edit `ds-qualifier/config.php` to customize:
- Question text
- Domain definitions
- Tooltips and help text

## Dependencies

### PHP Requirements
- **PHP**: ^8.1
- **Extensions**: ext-json

### Composer Packages
- **monolog/monolog** (^3.5): Logging framework
- **dompdf/dompdf** (^3.1): PDF report generation

### Frontend Libraries (CDN)
- jQuery 3.6.0
- jQuery UI 1.13.2
- Font Awesome 8.x
- Bootstrap (included locally)
- PatternFly (included locally)

## Security Features

- **Input Validation**: Comprehensive sanitization of all user inputs
- **CSRF Protection**: Session-based CSRF token validation
- **Secure Headers**: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
- **Path Traversal Prevention**: Secure file path handling
- **Error Logging**: Detailed logging without exposing sensitive data
- **Session Timeout**: Automatic session expiration (1 hour)
- **Secure File Operations**: Atomic file writes with rollback capability

## Comparison with Full Viewfinder

| Feature | Full Viewfinder | Viewfinder Lite |
|---------|----------------|-----------------|
| Profile Management | ✓ | ✗ |
| Full Maturity Assessments | ✓ | ✗ |
| Readiness Assessment | ✓ | ✓ |
| Digital Sovereignty Quiz | ✓ | ✗ |
| Operation Sovereign Shield | ✓ | ✗ |
| Compliance Framework Mapping | ✓ | ✗ |
| Line of Business Content | ✓ | ✗ |
| Approximate Size | ~100+ MB | ~60-65 MB |

## Troubleshooting

### Common Issues

**Issue**: Permission denied errors
```bash
# Solution: Set correct ownership and permissions
sudo chown -R apache:apache /var/www/html/viewfinder-lite
sudo chmod 755 /var/www/html/viewfinder-lite
sudo chmod 775 /var/www/html/viewfinder-lite/logs
```

**Issue**: Composer dependencies not found
```bash
# Solution: Run composer install
cd /var/www/html/viewfinder-lite
composer install --no-dev --optimize-autoloader
```

**Issue**: PDF generation fails
```bash
# Solution: Check dompdf is installed
composer show dompdf/dompdf
# If not found, reinstall dependencies
composer install --no-dev --optimize-autoloader
```

**Issue**: Sessions not persisting
```bash
# Solution: Check session directory permissions
sudo chmod 1733 /var/lib/php/session  # For RHEL/CentOS
sudo chmod 1733 /var/lib/php/sessions # For Debian/Ubuntu
```

### Logging

View application logs for troubleshooting:

```bash
# View recent logs
tail -f /var/www/html/viewfinder-lite/logs/app.log

# Search for errors
grep ERROR /var/www/html/viewfinder-lite/logs/app.log

# View web server logs
tail -f /var/log/httpd/error_log    # Apache (RHEL/CentOS)
tail -f /var/log/apache2/error.log  # Apache (Debian/Ubuntu)
tail -f /var/log/nginx/error.log    # Nginx
```

## Development

### Adding Custom Questions

1. Edit `ds-qualifier/config.php`
2. Add questions to the appropriate domain
3. Follow the existing format:
   ```php
   'questions' => [
       [
           'id' => 'unique-id',
           'text' => 'Your question text?',
           'tooltip' => 'Helpful explanation'
       ]
   ]
   ```

### Customizing Styling

- **Main application**: Edit `css/style.css`
- **Assessment interface**: Edit `ds-qualifier/css/ds-qualifier.css`
- **Dark theme**: Edit `css/tab-dark.css`

### Modifying Maturity Levels

Edit `ds-qualifier/results.php` to adjust:
- Score thresholds
- Maturity level names
- Recommendations per level

## Contributing

We welcome contributions from the community! This is an open source project and we encourage:

### Ways to Contribute

- **Report Issues**: Found a bug? [Open an issue](https://github.com/redhat-cop/viewfinder-lite/issues)
- **Suggest Features**: Have ideas for improvements? We'd love to hear them
- **Submit Pull Requests**: Code contributions are welcome
  - Add new questions or refine existing ones
  - Improve maturity level descriptions
  - Enhance the user interface
  - Fix bugs or improve performance
  - Translate to other languages
- **Share Feedback**: Help us improve by sharing your assessment experience
- **Contribute Domain Expertise**: Help refine questions and recommendations for specific domains

### Contribution Guidelines

1. **Fork the repository** and create a feature branch
2. **Make your changes** with clear, descriptive commit messages
3. **Test thoroughly** to ensure nothing breaks
4. **Submit a pull request** with a description of your changes
5. **Engage in discussion** - we'll review and provide feedback

### Code of Conduct

This project follows the [Red Hat Community of Practice Code of Conduct](https://github.com/redhat-cop). We are committed to providing a welcoming and inclusive environment for all contributors.

### Questions?

- **GitHub Discussions**: Ask questions and discuss ideas
- **GitHub Issues**: Report bugs and request features
- **Community**: Join the Red Hat Community of Practice

## License

Apache-2.0 License - Red Hat

This project is licensed under the Apache License 2.0. See the LICENSE file for details.

## Support

This is a community-supported open source project. For issues, questions, or feature requests:

- **GitHub Issues**: https://github.com/redhat-cop/viewfinder-lite/issues
- **GitHub Discussions**: https://github.com/redhat-cop/viewfinder-lite/discussions
- **Red Hat Community of Practice**: https://github.com/redhat-cop

For enterprise support and the enhanced CMMI version, contact your Red Hat representative.

## Disclaimer

This Digital Sovereignty Readiness Assessment Tool is provided by Red Hat for informational purposes only to help organizations review their general sovereign posture. It cannot be used to validate an organization's compliance with any specific sovereignty requirements. It is not endorsed by any regulatory authority, and its findings or recommendations do not constitute legal advice. Red Hat bears no legal responsibility or liability for the results or its use. No identity data will be collected or saved.

---

**Viewfinder Lite** - Streamlined Digital Sovereignty Readiness Assessment

Version: 1.0.0
