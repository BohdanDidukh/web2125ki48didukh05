const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = process.env.PORT || 3000;

const MIME_TYPES = {
    '.html': 'text/html',
    '.css': 'text/css',
    '.js': 'text/javascript',
    '.json': 'application/json',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.gif': 'image/gif',
    '.svg': 'image/svg+xml'
};

const requestHandler = (req, res) => {
    console.log(`${req.method} ${req.url}`);
    
    if (req.method === 'POST') {
        if (req.url === '/api/post-data.json') {
            let body = '';
            req.on('data', chunk => {
                body += chunk.toString();
            });
            
            req.on('end', () => {
                try {
                    const data = JSON.parse(body);
                    console.log('POST data:', data);
                    
                    const response = {
                        id: Math.floor(Math.random() * 1000),
                        status: 'success',
                        message: 'Data successfully saved on the server',
                        timestamp: new Date().toISOString()
                    };
                    
                    res.writeHead(200, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify(response));
                } catch (e) {
                    res.writeHead(400, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ error: 'Invalid JSON format' }));
                }
            });
            
            return;
        } else if (req.url === '/post-page.html') {
            let body = '';
            req.on('data', chunk => {
                body += chunk.toString();
            });
            
            req.on('end', () => {
                let filePath = path.join(__dirname, 'post-page.html');
                fs.readFile(filePath, (err, content) => {
                    if (err) {
                        res.writeHead(500);
                        res.end('Server error');
                        return;
                    }
                    
                    res.writeHead(200, { 'Content-Type': 'text/html' });
                    res.end(content);
                });
            });
            
            return;
        }
    }
    
    let url = req.url;
    
    if (url === '/') {
        url = '/index.html';
    }
    
    if (url.startsWith('/api/get-data.json')) {
        const urlObj = new URL(url, `http://${req.headers.host}`);
        const query = urlObj.searchParams.get('query') || '';
        
        console.log('GET query:', query);
        
        const response = {
            id: Math.floor(Math.random() * 1000),
            result: `Дані для запиту \"${query}\"`,
            timestamp: new Date().toISOString(),
            status: 'success'
        };
        
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify(response));
        return;
    }
    
    const filePath = path.join(__dirname, url);
    const extname = path.extname(filePath);
    
    fs.access(filePath, fs.constants.F_OK, (err) => {
        if (err) {
            res.writeHead(404);
            res.end('File not found');
            return;
        }
        
        const contentType = MIME_TYPES[extname] || 'application/octet-stream';
        
        fs.readFile(filePath, (err, content) => {
            if (err) {
                res.writeHead(500);
                res.end('Server error');
                return;
            }
            
            res.writeHead(200, { 'Content-Type': contentType });
            res.end(content);
        });
    });
}

if (require.main === module) {
    const server = http.createServer(requestHandler);
    server.listen(PORT, () => {
        console.log(`Server is running on port ${PORT}`);
        console.log(`Open http://localhost:${PORT} in your browser`);
    });
}

module.exports = requestHandler; 