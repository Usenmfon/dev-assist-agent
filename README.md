# Dev Assist — AI Code Helper Agent

Dev Assist is an intelligent AI assistant built with **Laravel** and **Neuron AI**, integrated into **Telex.im** via the A2A protocol. It helps developers with debugging, explaining code snippets, and generating concise, context-aware solutions.

---

## 🚀 Features

- 🤖 Built using **Neuron AI (Gemini model)** for intelligent responses.
- 💬 A2A-compliant webhook endpoint for **Telex.im**.
- 🧠 Detects user intent (explain, fix, generate).
- 📦 Stores interactions in the database.
- 🌐 Exposed publicly via **Expose** and **Render** for Telex integration.

---

## ⚙️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/Usenmfon/dev-assist-agent
cd dev-assist-agent
```

### 2. Install Dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Configure Environment

Add the following to your `.env` file:

```env
APP_URL=https://your-expose-or-render-url.com
GEMINI_API_KEY=your_gemini_api_key
GEMINI_MODEL=gemini-2.5-flash
TELEX_API_URL=https://api.telex.im
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Start the Server

```bash
php artisan serve
```

Or if using **Expose**:

```bash
expose share http://127.0.0.1:8000
```

---

## 🧩 Telex Workflow JSON Example

```json
{
  "active": true,
  "category": "utilities",
  "description": "AI Code Helper Agent built with Laravel + Neuron AI",
  "id": "dev_assist_agent",
  "long_description": "An intelligent assistant that helps developers by answering code questions and suggesting solutions directly in Telex.",
  "name": "dev_assist_agent",
  "nodes": [
    {
      "id": "dev_assist_node",
      "name": "Dev Assist Node",
      "parameters": {},
      "position": [500, 200],
      "type": "a2a/mastra-a2a-node",
      "typeVersion": 1,
      "url": "https://your-public-url.com/api/dev-assist/webhook"
    }
  ],
  "pinData": {},
  "settings": {
    "executionOrder": "v1"
  },
  "short_description": "An AI code helper for developers."
}
```

---

## 🔁 A2A Request Format

Telex sends a POST request like this:

```json
{
  "jsonrpc": "2.0",
  "id": "dev_assist_node",
  "method": "message/send",
  "params": {
    "message": {
      "kind": "message",
      "role": "user",
      "parts": [
        {
          "kind": "text",
          "text": "Explain this PHP code..."
        }
      ],
      "messageId": "msg-001",
      "taskId": "task-001"
    }
  }
}
```

Your agent should return a response like this:

```json
{
  "jsonrpc": "2.0",
  "id": "dev_assist_node",
  "result": {
    "kind": "message",
    "role": "assistant",
    "parts": [
      {
        "kind": "text",
        "text": "Here’s the explanation for your PHP code..."
      }
    ],
    "messageId": "msg-001",
    "taskId": "task-001"
  }
}
```

---

## 🧠 Intent Detection

| Keyword     | Intent          | Action |
|--------------|----------------|--------|
| explain      | `explain_code`  | Explains given code |
| generate     | `generate_code` | Generates new code |
| fix          | `fix_code`      | Suggests fixes |
| (default)    | `general`       | General developer Q&A |

---

## 📡 Endpoint Example

**POST** `/api/a2a/agent/dev_assist`

| Field       | Type   | Description |
|--------------|--------|-------------|
| channel_id   | string | Telex channel UUID |
| user_id      | string | User UUID |
| message      | string | The user message |

**Response Example:**

```json
{
  "status": "success",
  "response": "Here's your code explanation..."
}
```

---

## 🧾 Logging

Logs are stored in `/storage/logs/laravel.log` and can be viewed in the dashboard via `/logs` endpoint.

---

## 🧑‍💻 Technologies Used

- Laravel 11
- Neuron AI (`neuron-core/neuron-ai`)
- Gemini (Model: `gemini-2.5-flash`)
- Expose / Render (for public access)
- Telex.im (A2A protocol)

---

## 📚 Author

**Usenmfon Uko**  
Software Engineer | AI Developer  
[Telex Agent Link](https://telex.im/chessassist/dm/019a540b-a5aa-79bc-a295-a24d0ceb5969/019a540b-8829-79b7-ba4b-5f3af5a3c107)

---

## 🏁 License

MIT License © 2025 Usenmfon Uko
