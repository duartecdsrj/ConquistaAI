require('http').get('http://127.0.0.1:9000/', (response) => {
  response.resume();
  process.exit(response.statusCode === 200 ? 0 : 1);
}).on('error', () => process.exit(1));
