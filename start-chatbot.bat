@echo off
title SkinCheck AI Chatbot Server
echo ====================================================
echo Starting SkinCheck AI Chatbot Server (Node.js)...
echo API Engine: Google Gemini AI
echo Port: 5000
echo ====================================================
cd /d "%~dp0\chatbot-server"
if not exist node_modules (
    echo Installing dependencies...
    npm install
)
node server.js
pause
