set -e

echo "Starting CI process..."

echo "Installing dependencies..."
npm install

echo "Running tests..."
npm test

echo "Building project..."
npm run build

echo "Starting server for smoke test..."
npm start &
SERVER_PID=$!

sleep 3

echo "Running smoke test..."
curl -s http://localhost:3000 > /dev/null
if [ $? -eq 0 ]; then
    echo "Smoke test passed!"
else
    echo "Smoke test failed!"
    kill $SERVER_PID
    exit 1
fi

kill $SERVER_PID

echo "CI process completed successfully!" 