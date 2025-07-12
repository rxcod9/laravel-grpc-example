# Laravel gRPC Example (Publisher ↔ Consumer)

This repository demonstrates a **minimal yet production-ready gRPC integration** using **Laravel** with **RoadRunner** and **Spiral PHP gRPC**. It contains two Laravel Sail applications:

- `publisher-app`: Publishes gRPC messages
- `consumer-app`: Listens and processes gRPC messages

Both services communicate via `gRPC` using **Protocol Buffers**.

---

## 🧱 Architecture

```
[publisher-app] Laravel → Spiral\GRPC\Client
    |
    |   gRPC over TCP (default port 50051)
    v
[consumer-app] Laravel + RoadRunner gRPC server
```

- Laravel Sail (Docker-based)
- `spiral/roadrunner` for gRPC server
- `namely/protoc-all` for generating PHP stubs
- No PECL compilation; all `.so` files prebuilt and compressed
- Protos are **local per service**, not shared

---

## ⚙️ Prerequisites

- PHP 8.4+ (via Sail)
- Docker + Docker Compose
- Composer
- Laravel Sail
- ✅ No need for local `grpc.so` / `protobuf.so` or PECL tools

---

## 🚀 Quick Setup

### 1. Clone the Repository

```bash
git clone https://github.com/rxcod9/laravel-grpc-example.git
cd laravel-grpc-example
```

### 2. Install PHP and Composer Dependencies

#### Installing directly:

```sh
cd publisher-app && composer install
cd consumer-app && composer install
```

#### Or Installing Composer Dependencies through `docker`:

If you do not have php8.4 in host and wants to use docker with php8.4 for installing composer.
```sh
cd publisher-app
docker run --rm \
        --pull=always \
        -v "$(pwd)":/opt \
        -w /opt \
        laravelsail/php84-composer:latest \
        bash -c "composer install"

cd consumer-app
docker run --rm \
        --pull=always \
        -v "$(pwd)":/opt \
        -w /opt \
        laravelsail/php84-composer:latest \
        bash -c "composer install"
```

### 3. Generate PHP gRPC Stubs

When you add.modify .proto files, you need to generate these files

#### Using `protoc` directly:

```sh
# For publisher-app
cd publisher-app/
protoc --proto_path=./protos \
  --plugin=protoc-gen-php=$(which protoc-gen-php) \
  --plugin=protoc-gen-php-grpc=/usr/local/bin/protoc-gen-php-grpc \
  --php_out=./app/Grpc \
  --php-grpc_out=./app/Grpc \
  ./protos/messages.proto

# For consumer-app
cd consumer-app/
protoc --proto_path=./protos \
  --plugin=protoc-gen-php=$(which protoc-gen-php) \
  --plugin=protoc-gen-php-grpc=/usr/local/bin/protoc-gen-php-grpc \
  --php_out=./app/Grpc \
  --php-grpc_out=./app/Grpc \
  ./protos/messages.proto
```

#### Or using Docker:

```sh
cd publisher-app/
docker run --rm \
    -v "$PWD:/workspace" \
    -w /workspace namely/protoc-all \
    -f protos/messages.proto \
    -l php \
    -o app/Grpc

cd consumer-app/
docker run --rm \
    -v "$PWD:/workspace" \
    -w /workspace namely/protoc-all \
    -f protos/messages.proto \
    -l php \
    -o app/Grpc
```

> This uses `namely/protoc-all` to avoid installing Protobuf toolchains locally.


### 4. Start the Applications with Sail

```sh
cd publisher-app
./vendor/bin/sail up -d
cd consumer-app
./vendor/bin/sail up -d
```

---

## 🐘 Prebuilt PHP Extensions

The `.so` files for `grpc` and `protobuf` are:

- Precompiled
- Compressed into `grpc-protobuf.tar.gz`
- Extracted into `/usr/lib/php/20240924` inside the Sail containers
- Auto-enabled in `/etc/php/8.3/cli/conf.d/99-grpc.ini`

This avoids long PECL install times and keeps builds deterministic.

---

## 📦 Artisan Command: Send Message

You can test the gRPC publisher:

```bash
cd publisher-app
./vendor/bin/sail artisan grpc:publish-message notifications '{"event":"user.registered"}'
```

Expected output:

```json
{
  "success": true,
  "message": "Message sent to consumer via gRPC"
}
```

---

## 🧪 Debugging & Logs

To tail consumer logs:

```bash
cd consumer-app
./vendor/bin/sail logs -f
```

Verify gRPC extension is loaded:

```bash
./vendor/bin/sail php -m | grep grpc
./vendor/bin/sail php -m | grep protobuf
```

---

## 🛑 Stopping Services

```bash
cd publisher-app && ./vendor/bin/sail down
cd consumer-app && ./vendor/bin/sail down
```

---

## 📂 Project Structure

```bash
laravel-grpc-example/
├── consumer-app/        # Laravel gRPC server with RoadRunner + Supervisor
│   ├── app/Grpc/        # Generated gRPC stub code
│   ├── protos/          # Local proto definitions
│   └── start-container  # Entrypoint
└── publisher-app/       # Laravel gRPC client
    ├── app/Grpc/        # Generated gRPC stub code
    ├── protos/          # Local proto definitions
    └── grpc:publish-message command
```

---

## 🌍 Environment Configuration

Make sure these `.env` variables are set:

**consumer-app/.env**
```env
GRPC_PORT=50051
```

**publisher-app/.env**
```env
CONSUMER_PORT=50051
```

---

## 🛠 RoadRunner gRPC Server Setup (consumer-app)

1. RoadRunner runs via Supervisor
2. On container start:
   - `bootstrap.php` is invoked
   - Registers `MessageService`
   - Logs to stdout

---

## 📈 Future Improvements

- Use `.proto` sync between services (e.g., via `@shared-protos` git submodule)
- Add TLS support (gRPC + secure channels)
- Use GitHub Actions to generate `.so` into GitHub Releases
- Implement streaming/bidirectional gRPC calls
- Integrate with Laravel Events/Queue workers

---

## 🧠 Got Questions?

Feel free to open an [issue](https://github.com/rxcod9/laravel-grpc-example/issues) or discuss ideas for enhancements. Contributions are welcome!

---

## 📜 License

MIT — free to use, modify, and distribute.