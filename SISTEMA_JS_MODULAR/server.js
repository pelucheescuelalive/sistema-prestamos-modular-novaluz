/**
 * 🚀 SERVIDOR NODE.JS PARA SISTEMA JAVASCRIPT PURO
 * Servidor HTTP simple que sirve la aplicación SPA correctamente
 */

const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = 8090;

// MIME types para diferentes archivos
const mimeTypes = {
  '.html': 'text/html',
  '.js': 'application/javascript',
  '.css': 'text/css',
  '.json': 'application/json',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon'
};

const server = http.createServer((req, res) => {
  console.log(`📥 ${req.method} ${req.url}`);
  
  // Para SPA: todas las rutas que no son archivos van a index.html
  let filePath = req.url === '/' ? '/index.html' : req.url;
  
  // Remover query parameters
  filePath = filePath.split('?')[0];
  
  const fullPath = path.join(__dirname, filePath);
  const ext = path.extname(filePath).toLowerCase();
  const contentType = mimeTypes[ext] || 'application/octet-stream';

  // Verificar si el archivo existe
  fs.access(fullPath, fs.constants.F_OK, (err) => {
    if (err) {
      // Si no existe el archivo y no es una extensión conocida, servir index.html (SPA routing)
      if (!ext || ext === '.html') {
        const indexPath = path.join(__dirname, 'index.html');
        fs.readFile(indexPath, (err, content) => {
          if (err) {
            res.writeHead(500);
            res.end('Error interno del servidor');
            return;
          }
          res.writeHead(200, { 'Content-Type': 'text/html' });
          res.end(content, 'utf-8');
        });
      } else {
        res.writeHead(404);
        res.end('Archivo no encontrado');
      }
    } else {
      // El archivo existe, servirlo
      fs.readFile(fullPath, (err, content) => {
        if (err) {
          res.writeHead(500);
          res.end('Error del servidor: ' + err.code);
        } else {
          res.writeHead(200, { 'Content-Type': contentType });
          res.end(content, 'utf-8');
        }
      });
    }
  });
});

server.listen(PORT, () => {
  console.log('🚀 ===============================================');
  console.log('🎯 SERVIDOR NODE.JS INICIADO CORRECTAMENTE');
  console.log('🌐 Sistema JavaScript puro disponible en:');
  console.log(`   💻 http://localhost:${PORT}`);
  console.log('📁 Sirviendo desde: SISTEMA_JS_MODULAR');
  console.log('✅ SPA (Single Page Application) configurado');
  console.log('🚀 ===============================================');
});

// Manejo de errores
server.on('error', (err) => {
  if (err.code === 'EADDRINUSE') {
    console.error(`❌ Puerto ${PORT} ya está en uso`);
    console.log('💡 Intenta con otro puerto o cierra el proceso que lo usa');
  } else {
    console.error('❌ Error del servidor:', err);
  }
});
