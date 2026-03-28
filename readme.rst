# CodeIgniter Docker Setup (PHP 7.3 + MySQL 8)

This project sets up a CodeIgniter application using Docker with:

* PHP 7.3 (Apache)
* MySQL 8
* Docker Compose

---

## 📁 Project Structure

```
ci-docker/
├── app/                   
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## ⚙️ Prerequisites

Make sure you have installed:

* Docker

Check installation:

```
docker -v
docker compose version
```

---

## ▶️ Run the Application

```
docker compose up -d --build
```

---

## 🌐 Access the App

Open your browser and visit:

```
http://localhost:8080
```
