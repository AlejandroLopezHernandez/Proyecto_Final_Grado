import mysql.connector
import sys
import os

sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from carta_renderer import CartaRenderer

def generar_html_error(mensaje):
    return f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body {{ font-family: Arial, sans-serif; padding: 20px; }}
            h1 {{ color: #d9534f; }}
            pre {{ background: #f8f9fa; padding: 15px; border-radius: 5px; }}
        </style>
    </head>
    <body>
        <h1>Error al generar la carta</h1>
        <p>{mensaje}</p>
    </body>
    </html>
    """

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="1234",
        database="ClickComandaDB"
    )
    
    print("Conexión a BD exitosa", file=sys.stderr)
    
    # Crear el renderer
    renderer = CartaRenderer(conn)
    
    # Generar la carta con verificación
    print("Generando carta...", file=sys.stderr)
    html = renderer.render_carta("Restaurante El Cañaveral")
    
    if html is None:
        raise ValueError("El método render_carta() devolvió None")
    
    # Guardar el HTML generado
    html_path = os.path.join(os.path.dirname(__file__), "IndexCarta.html")
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(html)
    
    print(f"Carta generada en: {html_path}", file=sys.stderr)
    
    # Cerrar conexión
    conn.close()
    
    print("✓ Carta generada exitosamente", file=sys.stderr)
    
except Exception as e:
    print(f"ERROR: {str(e)}", file=sys.stderr)
    html_path = os.path.join(os.path.dirname(__file__), "IndexCarta.html")
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(generar_html_error(str(e)))