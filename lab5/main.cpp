#include <iostream>
#include <fstream>
#include <sstream>
#include <string>
#include <map>
#include <thread>
#include <chrono>
#include <regex>
#include <winsock2.h>
#include <ws2tcpip.h>
#include <sys/stat.h>

#pragma comment(lib, "ws2_32.lib")

const int PORT = 8080;
const std::string WEB_ROOT = "./";

bool endsWith(const std::string& str, const std::string& suffix) {
    return str.size() >= suffix.size() &&
           str.compare(str.size() - suffix.size(), suffix.size(), suffix) == 0;
}

bool startsWith(const std::string& str, const std::string& prefix) {
    return str.size() >= prefix.size() &&
           str.compare(0, prefix.size(), prefix) == 0;
}

bool fileExists(const std::string& path) {
    struct stat buffer;
    return (stat(path.c_str(), &buffer) == 0 && !(buffer.st_mode & S_IFDIR));
}

std::string getMimeType(const std::string& path) {
    if (endsWith(path, ".html")) return "text/html";
    if (endsWith(path, ".css")) return "text/css";
    if (endsWith(path, ".js")) return "application/javascript";
    if (endsWith(path, ".png")) return "image/png";
    if (endsWith(path, ".jpg")) return "image/jpeg";
    return "text/plain";
}

std::string urlDecode(const std::string& str) {
    std::string result;
    char ch;
    int i, j;
    for (i = 0; i < (int)str.length(); i++) {
        if (str[i] == '%') {
            sscanf(str.substr(i + 1, 2).c_str(), "%x", &j);
            ch = static_cast<char>(j);
            result += ch;
            i += 2;
        } else if (str[i] == '+') {
            result += ' ';
        } else {
            result += str[i];
        }
    }
    return result;
}

std::map<std::string, std::string> parseFormData(const std::string& data) {
    std::map<std::string, std::string> result;
    std::stringstream ss(data);
    std::string item;

    while (std::getline(ss, item, '&')) {
        size_t pos = item.find('=');
        if (pos != std::string::npos) {
            std::string key = urlDecode(item.substr(0, pos));
            std::string value = urlDecode(item.substr(pos + 1));
            result[key] = value;
        }
    }

    return result;
}

std::string readFile(const std::string& path) {
    std::ifstream file(path.c_str(), std::ios::binary);
    if (!file) return "";
    std::ostringstream ss;
    ss << file.rdbuf();
    return ss.str();
}

void sendResponse(SOCKET clientSocket, const std::string& status, const std::string& contentType, const std::string& body) {
    std::ostringstream response;
    response << "HTTP/1.1 " << status << "\r\n";
    response << "Content-Type: " << contentType << "\r\n";
    response << "Content-Length: " << body.size() << "\r\n";
    response << "Connection: close\r\n\r\n";
    response << body;
    send(clientSocket, response.str().c_str(), (int)response.str().length(), 0);
}

void handleClient(SOCKET clientSocket) {
    char buffer[4096] = {0};
    recv(clientSocket, buffer, 4096, 0);
    std::string request(buffer);

    std::istringstream requestStream(request);
    std::string method, path, protocol;
    requestStream >> method >> path >> protocol;

    std::string body;
    size_t contentLength = 0;

    if (request.find("Content-Length:") != std::string::npos) {
        size_t pos = request.find("Content-Length:") + 16;
        contentLength = std::stoi(request.substr(pos, request.find("\r\n", pos)));
        size_t bodyStart = request.find("\r\n\r\n");
        if (bodyStart != std::string::npos) {
            body = request.substr(bodyStart + 4);
            while (body.size() < contentLength) {
                char more[1024];
                int len = recv(clientSocket, more, 1024, 0);
                if (len <= 0) break;
                body.append(more, len);
            }
        }
    }

    if (path == "/" || path == "/home") {
        std::string html = readFile(WEB_ROOT + "home.html");
        sendResponse(clientSocket, "200 OK", "text/html", html);
    } else if (startsWith(path, "/get?")) {
        std::string query = path.substr(5);
        auto params = parseFormData(query);
        std::string name = params["name"];
        std::string reply = "<h2>Hello, " + name + "! This is a GET response.</h2>";
        sendResponse(clientSocket, "200 OK", "text/html", reply);
    } else if (path == "/post" && method == "POST") {
        auto params = parseFormData(body);
        std::string name = params["name"];
        std::string email = params["email"];
        std::string message = params["message"];
        std::ostringstream response;
        response << "<h2>POST Received!</h2>";
        response << "<p>Name: " << name << "</p>";
        response << "<p>Email: " << email << "</p>";
        response << "<p>Message: " << message << "</p>";
        sendResponse(clientSocket, "200 OK", "text/html", response.str());
    } else {
        std::string filePath = WEB_ROOT + path.substr(1);
        if (fileExists(filePath)) {
            std::string content = readFile(filePath);
            sendResponse(clientSocket, "200 OK", getMimeType(filePath), content);
        } else {
            sendResponse(clientSocket, "404 Not Found", "text/plain", "404 Page not found");
        }
    }

    closesocket(clientSocket);
}

int main() {
    WSADATA wsaData;
    if (WSAStartup(MAKEWORD(2,2), &wsaData) != 0) {
        std::cerr << "WSAStartup failed.\n";
        return 1;
    }

    SOCKET server_fd = socket(AF_INET, SOCK_STREAM, 0);
    if (server_fd == INVALID_SOCKET) {
        std::cerr << "Socket creation failed.\n";
        WSACleanup();
        return 1;
    }

    sockaddr_in address = {0};
    int opt = 1;
    int addrlen = sizeof(address);
    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(PORT);

    setsockopt(server_fd, SOL_SOCKET, SO_REUSEADDR, (char*)&opt, sizeof(opt));

    if (bind(server_fd, (sockaddr*)&address, sizeof(address)) == SOCKET_ERROR) {
        std::cerr << "Bind failed.\n";
        closesocket(server_fd);
        WSACleanup();
        return 1;
    }

    if (listen(server_fd, 10) == SOCKET_ERROR) {
        std::cerr << "Listen failed.\n";
        closesocket(server_fd);
        WSACleanup();
        return 1;
    }

    std::cout << "Server started on port " << PORT << "\n";

    while (true) {
        SOCKET clientSocket = accept(server_fd, (sockaddr*)&address, &addrlen);
        if (clientSocket == INVALID_SOCKET) {
            std::cerr << "Accept failed.\n";
            continue;
        }
    
        handleClient(clientSocket);
    }    

    closesocket(server_fd);
    WSACleanup();
    return 0;
}