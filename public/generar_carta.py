import mysql.connector
import sys
import os

# Agregar el directorio actual al path para importar carta_renderer
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from carta_renderer import CartaRenderer

try:
    # Conectar a la base de datos
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="1234",
        database="ClickComandaDB"
    )
    
    print("Conexión a BD exitosa", file=sys.stderr)
    
    # Crear el renderer
    renderer = CartaRenderer(conn)
    
    # Generar la carta
    print("Generando carta...", file=sys.stderr)
    html = renderer.render_carta("Restaurante El Cañaveral")
    
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
    # Crear un HTML de error
    error_html = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
    </head>
    <body>
        <h1>Error al generar la carta</h1>
        <p>{str(e)}</p>
    </body>
    </html>
    """
    html_path = os.path.join(os.path.dirname(__file__), "IndexCarta.html")
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(error_html)