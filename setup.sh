#!/bin/bash
# Setup script for girlfriend_surprise project

set -e

echo "🎁 Girlfriend Surprise - Setup Script"
echo "====================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "✨ Creating .env from .env.example..."
    cp .env.example .env
    echo "📝 Please edit .env with your database credentials"
else
    echo "✅ .env already exists"
fi

echo ""
echo "🐳 Checking Docker..."
if command -v docker &> /dev/null; then
    echo "✅ Docker found"
    echo ""
    echo "Starting MySQL + phpMyAdmin..."
    docker compose up -d
    echo "✅ Services started!"
    echo "   phpMyAdmin: http://localhost:8080"
    echo "   Credentials: root / example_root_password"
else
    echo "⚠️  Docker not found - install from https://www.docker.com/"
    echo ""
    echo "🔧 Alternatively, using XAMPP MySQL:"
    echo "   1. Start XAMPP MySQL service"
    echo "   2. Open phpMyAdmin at http://localhost/phpmyadmin"
    echo "   3. Create database 'girlfriend_surprise'"
    echo "   4. Import init.sql file"
fi

echo ""
echo "📂 Checking folder permissions..."
if [ ! -d img ]; then
    mkdir -p img
    echo "✅ Created img/ folder"
else
    echo "✅ img/ folder exists"
fi

if [ ! -d data ]; then
    mkdir -p data
    echo "✅ Created data/ folder"
else
    echo "✅ data/ folder exists"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Open http://localhost/girlfriend_surprise/index.php in browser"
echo "2. Add photos to img/ folder or use 'Add Picture' button"
echo "3. Customize messages in index.php"
echo "4. Share with your special someone! 💕"
echo ""
echo "Need help? See README.md for detailed instructions"
