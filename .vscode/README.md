# VS Code Debug Configuration for Laravel

This Laravel project includes VS Code debugging configurations to run `php artisan serve` on port 8777 and automatically open Microsoft Edge for debugging.

## 🚀 Available Debug Configurations

### 1. **Launch Laravel Server (Port 8777)**
- Runs `php artisan serve` on port 8777
- Opens the server URL automatically in your default browser
- Sets `APP_ENV=local` and `APP_DEBUG=true`

### 2. **PHP Xdebug Listen**
- Listens for Xdebug connections on port 9003
- Perfect for debugging individual requests
- Excludes vendor files from debugging

### 3. **Laravel with Xdebug (Port 8777)**
- Combines Laravel server with Xdebug debugging
- Starts server with Xdebug enabled
- Opens browser automatically

### 4. **🚀 Laravel + Edge Browser** (Compound)
- Runs Laravel server as a background task
- Automatically opens Microsoft Edge after 3 seconds
- Best option for quick development and testing

## 🛠️ How to Use

### Method 1: Using VS Code Debug Panel
1. Open VS Code in your Laravel project
2. Go to the Debug panel (Ctrl+Shift+D)
3. Select "🚀 Laravel + Edge Browser" from the dropdown
4. Press F5 or click the green play button
5. Wait for the server to start and Edge to open automatically

### Method 2: Using Command Palette
1. Press Ctrl+Shift+P
2. Type "Debug: Select and Start Debugging"
3. Choose your desired configuration
4. The server will start and browser will open

## 📋 Available Tasks

You can also run these tasks individually:
- **Laravel Serve**: Starts the Laravel server on port 8777
- **Stop Laravel Server**: Kills any running Laravel server processes
- **open-edge-8777**: Opens Microsoft Edge to http://localhost:8777

## 🔧 Configuration Details

- **Server Host**: `0.0.0.0` (accessible from network)
- **Server Port**: `8777`
- **Xdebug Port**: `9003`
- **Browser**: Microsoft Edge
- **Environment**: Local development with debug enabled

## 🐛 Debugging Features

- **Breakpoints**: Set breakpoints in your PHP code
- **Step Debugging**: Step through code execution
- **Variable Inspection**: Inspect variables and their values
- **Call Stack**: View the execution call stack
- **Console Output**: See Laravel logs in the integrated terminal

## 📁 Files Created

- `.vscode/launch.json` - Debug configurations
- `.vscode/tasks.json` - Build and utility tasks
- `.vscode/settings.json` - PHP and Laravel specific settings

## 🚨 Requirements

- PHP with Xdebug extension (for debugging features)
- Microsoft Edge browser
- VS Code with PHP Debug extension

## 💡 Tips

1. **Quick Start**: Just press F5 and select "🚀 Laravel + Edge Browser"
2. **Stop Server**: Use Ctrl+C in the terminal or run "Stop Laravel Server" task
3. **Change Port**: Edit the port number in both `launch.json` and `tasks.json`
4. **Different Browser**: Replace `microsoft-edge` with your preferred browser command

Happy debugging! 🎉
