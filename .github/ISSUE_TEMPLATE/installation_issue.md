---
name: 🚀 Installation Issue
about: Report issues with the laravel-base:install command
title: '[INSTALL] '
labels: 'installation, bug, needs-triage'
assignees: ''

---

# 🚀 Installation Issue

## 📝 Problem Description
Describe what went wrong during the installation process.

## 🔄 Installation Steps
**Which installation method did you use?**
- [ ] `php artisan laravel-base:install` (full interactive installation)
- [ ] `php artisan laravel-base:install --skip-prompts` (automated with defaults)
- [ ] Manual installation following README

## 📍 Installation Stage
**At which stage did the issue occur?**
- [ ] Environment file setup (.env creation/configuration)
- [ ] Application key generation
- [ ] Composer dependency installation
- [ ] NPM dependency installation
- [ ] Database setup/migration
- [ ] User account creation
- [ ] Application naming/configuration
- [ ] Final setup verification

## ❌ Error Details
**What error message(s) did you receive?**

### Console Output
```bash
# Paste the complete console output here, including the command you ran
```

### Error Messages
```
# Paste any specific error messages here
```

## 🔧 System Information

### Required Information
- **PHP Version**: [e.g., 8.3.23]
- **Composer Version**: [e.g., 2.8.1]
- **Node.js Version**: [e.g., 23.7.0]
- **NPM Version**: [e.g., 10.9.0]
- **OS**: [e.g., Ubuntu 22.04, macOS 14, Windows 11]

### Laravel Information
- **Laravel Version**: [Run `php artisan --version`]
- **PHP Extensions**: [Run `php -m` and list relevant extensions]

### Database Information (if database-related)
- **Database Type**: [e.g., SQLite, MySQL, PostgreSQL]
- **Database Version**: [if applicable]
- **Database Connection**: [local, remote, Docker]

## 📁 Project State
**What is the current state of your project?**
- [ ] Fresh Laravel installation
- [ ] Existing Laravel project
- [ ] Clean Laravel Base clone
- [ ] Modified Laravel Base setup

## 🔍 Attempted Solutions
**What have you tried to resolve this issue?**
- [ ] Cleared Laravel caches (`php artisan optimize:clear`)
- [ ] Reinstalled Composer dependencies
- [ ] Reinstalled NPM dependencies
- [ ] Checked file permissions
- [ ] Verified system requirements
- [ ] Tried manual installation steps

## 📋 Installation Responses
**If you got to the interactive prompts, what did you enter?**
Please share (without sensitive information):
- Application name: [e.g., "My Laravel App"]
- User creation: [Did you create a user? Y/N]
- Additional options selected: [Any other choices you made]

## 💾 Configuration Files
**Are there any relevant configuration details?**

### .env file (if created)
```env
# Only include non-sensitive configuration like APP_NAME, DB_CONNECTION, etc.
# DO NOT include APP_KEY, passwords, or other secrets
```

### composer.json requirements (if modified)
```json
// Any custom requirements you added
```

## 🤔 Expected Result
What did you expect to happen after running the installation command?

## 📚 Additional Context
- Are you following a specific tutorial or guide?
- Is this for a specific use case (learning, production, etc.)?
- Any custom requirements or modifications needed?

## ✅ Checklist
- [ ] I have provided complete console output
- [ ] I have verified my system meets the requirements
- [ ] I have tried basic troubleshooting steps
- [ ] I have not included sensitive information (passwords, keys, etc.)
- [ ] I have searched for similar installation issues
