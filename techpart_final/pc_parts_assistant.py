import gradio as gr
import ollama

# Define a strict but concise system instruction
SYSTEM_PROMPT = """You are a helpful and concise PC parts specialist in the Philippines.
Answer all questions in a short, friendly, and direct way.
Only respond to PC hardware topics (CPUs, GPUs, RAM, storage, motherboards, cooling, etc.).
If asked something unrelated, politely refuse."""

# Function to generate chatbot-like responses
def ask_pc_specialist(query):
    response = ollama.chat(
        model="mistral",  # Use Mistral for better responses (or llama2)
        messages=[
            {"role": "system", "content": SYSTEM_PROMPT},
            {"role": "user", "content": query}
        ]
    )
    
    return response["message"]["content"].strip()  # Ensures clean output

# Gradio UI
with gr.Blocks() as demo:
    gr.Markdown("# 💻 PC Parts Specialist Chatbot")
    gr.Markdown("Ask me anything about PC parts, builds, and upgrades! v2")

    inp = gr.Textbox(label="Your question:")
    out = gr.Textbox(label="Response", interactive=False)

    btn = gr.Button("Ask")
    btn.click(ask_pc_specialist, inputs=inp, outputs=out)

demo.launch(server_name="0.0.0.0", server_port=5000)  # Runs on localhost:7860
