const request = require('supertest');
const http = require('http');
const fs = require('fs');
const path = require('path');

jest.mock('fs', () => ({
  readFile: jest.fn((path, callback) => {
    callback(null, 'Mock file content');
  }),
  access: jest.fn((path, mode, callback) => {
    callback(null);
  }),
  constants: {
    F_OK: 0
  }
}));

const app = require('../server');

describe('Server Tests', () => {
  let server;

  beforeAll(() => {
    server = http.createServer(app);
    server.listen(3001);
  });

  afterAll((done) => {
    server.close(done);
  });

  test('GET / should return index.html', async () => {
    const response = await request(server).get('/');
    expect(response.status).toBe(200);
  });

  test('GET /api/get-data.json should return JSON data', async () => {
    const response = await request(server)
      .get('/api/get-data.json?query=test');
    
    expect(response.status).toBe(200);
    expect(response.headers['content-type']).toContain('application/json');
    expect(response.body).toHaveProperty('status', 'success');
  });

  test('POST /api/post-data.json should process data and return JSON', async () => {
    const response = await request(server)
      .post('/api/post-data.json')
      .send({
        name: 'Test User',
        message: 'This is a test message',
        timestamp: new Date().toISOString()
      });
    
    expect(response.status).toBe(200);
    expect(response.headers['content-type']).toContain('application/json');
    expect(response.body).toHaveProperty('status', 'success');
  });
}); 