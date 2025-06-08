import json
from datetime import datetime

class CartaRenderer:
    ICONOS = {
        "entrantes": "🥟", "vegetariano": "🥦", "vegano": "🌱",
        "entre_panes": "🍔", "mar": "🐟", "carnes": "🥩",
        "arroces_pastas": "🍝", "postres": "🍰",
        "cerveza": "🍺", "vinos": "🍷", "refrescos": "🧃",
        "cafes": "☕", "whiskey": "🥃", "ron": "🍹", "vodka": "🍶", "ginebra": "🍸"
    }

    def __init__(self, conexion):
        self.conexion = conexion
        self.cursor = conexion.cursor()

    def conectar(self):
        return self.conexion is not None

    def obtener_comidas(self):
        self.cursor.execute('SELECT * FROM comida')
        columns = [col[0] for col in self.cursor.description]
        rows = self.cursor.fetchall()
        comidas = [dict(zip(columns, row)) for row in rows]
        
        # DEBUG: Mostrar estructura de datos
        print("=== DEBUG COMIDAS ===")
        print(f"Columnas disponibles: {columns}")
        print(f"Total comidas: {len(comidas)}")
        if comidas:
            print("Primeras 3 comidas:")
            for i, c in enumerate(comidas[:3]):
                print(f"  {i+1}. Nombre: {c.get('nombre')}")
                print(f"     Categoría: {c.get('categoria')} (tipo: {type(c.get('categoria'))})")
                print(f"     Descripción: {c.get('descripcion', '')[:50]}...")
                print()
        
        return comidas

    def obtener_bebidas(self):
        self.cursor.execute('SELECT * FROM bebida')
        columns = [col[0] for col in self.cursor.description]
        rows = self.cursor.fetchall()
        bebidas = [dict(zip(columns, row)) for row in rows]
        
        # DEBUG: Mostrar estructura de datos
        print("=== DEBUG BEBIDAS ===")
        print(f"Columnas disponibles: {columns}")
        print(f"Total bebidas: {len(bebidas)}")
        if bebidas:
            print("Primeras 5 bebidas:")
            for i, b in enumerate(bebidas[:5]):
                print(f"  {i+1}. Nombre: {b.get('nombre')}")
                print(f"     Tipo: {b.get('tipo_bebida')}")
                print(f"     Descripción: {b.get('descripcion', '')[:50]}...")
                print()
        
        return bebidas

    def formatear_precio(self, pvp):
        return f"{pvp:.2f} €" if pvp else "-"

    def clasificar_destilado(self, bebida):
        """Clasifica un destilado según su descripción"""
        descripcion = (bebida.get("descripcion") or "").lower()
        nombre = (bebida.get("nombre") or "").lower()
        
        # Buscar palabras clave tanto en nombre como en descripción
        texto_completo = f"{nombre} {descripcion}"
        
        print(f"    Clasificando: {nombre[:30]} | Texto: {texto_completo[:50]}")
        
        if any(palabra in texto_completo for palabra in ["whiskey", "whisky", "bourbon"]):
            print(f"      -> WHISKEY")
            return "whiskey"
        elif any(palabra in texto_completo for palabra in ["ron", "rum"]):
            print(f"      -> RON")
            return "ron"
        elif any(palabra in texto_completo for palabra in ["vodka"]):
            print(f"      -> VODKA")
            return "vodka"
        elif any(palabra in texto_completo for palabra in ["ginebra", "gin"]):
            print(f"      -> GINEBRA")
            return "ginebra"
        else:
            print(f"      -> DESTILADOS (genérico)")
            return "destilados"

    def render_carta(self, nombre_restaurante="Restaurante El Cañaveral"):
        if not self.conectar():
            return "<html><body><h1>Error de conexión</h1></body></html>"

        comidas = self.obtener_comidas()
        bebidas = self.obtener_bebidas()

        # Inicializar categorías de comida
        categorias_comida = {
            "entrantes": [], "vegetariano": [], "vegano": [], "entre_panes": [],
            "mar": [], "carnes": [], "arroces_pastas": [], "postres": []
        }

        # Procesar comidas con DEBUG mejorado
        print("\n=== PROCESANDO CATEGORÍAS DE COMIDA ===")
        for i, c in enumerate(comidas):
            print(f"Procesando comida {i+1}: {c.get('nombre')}")
            categoria_raw = c.get("categoria", "")
            print(f"  Categoría raw: '{categoria_raw}' (tipo: {type(categoria_raw)})")
            
            cats = []
            
            # Intentar diferentes formas de parsear la categoría
            if isinstance(categoria_raw, str) and categoria_raw:
                # Primero intentar JSON
                try:
                    cats = json.loads(categoria_raw)
                    print(f"  Parseado como JSON: {cats}")
                except (json.JSONDecodeError, TypeError):
                    # Si no es JSON, tratar como string simple
                    cats = [categoria_raw]
                    print(f"  Tratado como string: {cats}")
            elif isinstance(categoria_raw, list):
                cats = categoria_raw
                print(f"  Ya era lista: {cats}")
            
            # Procesar cada categoría
            for cat in cats:
                cat_original = str(cat).strip()
                cat_normalizada = cat_original.lower().replace(" ", "_")
                
                print(f"    Procesando categoría: '{cat_original}' -> '{cat_normalizada}'")
                
                # Mapeo mejorado para variaciones comunes
                mapeo_categorias = {
                    "arroz": "arroces_pastas",
                    "pasta": "arroces_pastas", 
                    "arroces": "arroces_pastas",
                    "pastas": "arroces_pastas",
                    "arroces_y_pastas": "arroces_pastas",
                    "arroces_pastas": "arroces_pastas",
                    "pescado": "mar",
                    "mariscos": "mar",
                    "carne": "carnes",
                    "entrante": "entrantes",
                    "postre": "postres"
                }
                
                # Usar mapeo si existe, sino usar la categoría normalizada
                cat_final = mapeo_categorias.get(cat_normalizada, cat_normalizada)
                print(f"      Categoría final: '{cat_final}'")
                
                if cat_final in categorias_comida:
                    if c not in categorias_comida[cat_final]:
                        categorias_comida[cat_final].append(c)
                        print(f"      ✓ Agregado a {cat_final}")
                    else:
                        print(f"      - Ya existía en {cat_final}")
                else:
                    print(f"      ✗ Categoría '{cat_final}' no reconocida")

        # Inicializar bebidas por tipo
        bebidas_por_tipo = {
            "cerveza": [], "refrescos": [], "vinos": [], "cafes": [],
            "whiskey": [], "ron": [], "vodka": [], "ginebra": [], "destilados": []
        }

        # Procesar bebidas con DEBUG
        print("\n=== PROCESANDO BEBIDAS ===")
        for i, b in enumerate(bebidas):
            print(f"Procesando bebida {i+1}: {b.get('nombre')}")
            tipo = (b.get("tipo_bebida") or "").strip().lower()
            print(f"  Tipo: '{tipo}'")
            
            if tipo == "destilados":
                print(f"  Es destilado, clasificando...")
                categoria_destilado = self.clasificar_destilado(b)
                bebidas_por_tipo[categoria_destilado].append(b)
            elif tipo in bebidas_por_tipo:
                bebidas_por_tipo[tipo].append(b)
                print(f"  ✓ Agregado a {tipo}")
            else:
                # Mapeo adicional para nombres de tipos que puedan variar
                mapeo_tipos = {
                    "cerveza": "cerveza",
                    "vino": "vinos", 
                    "refresco": "refrescos",
                    "cafe": "cafes",
                    "café": "cafes",
                    "gaseosa": "refrescos",
                    "refresco": "refrescos"
                }
                tipo_mapeado = mapeo_tipos.get(tipo)
                if tipo_mapeado and tipo_mapeado in bebidas_por_tipo:
                    bebidas_por_tipo[tipo_mapeado].append(b)
                    print(f"  ✓ Mapeado y agregado a {tipo_mapeado}")
                else:
                    print(f"  ✗ Tipo '{tipo}' no reconocido")

        # Mostrar resumen final
        print("\n=== RESUMEN FINAL ===")
        print("COMIDAS:")
        for cat, items in categorias_comida.items():
            if items:
                print(f"  {cat}: {len(items)} items")
        
        print("\nBEBIDAS:")
        for tipo, items in bebidas_por_tipo.items():
            if items:
                print(f"  {tipo}: {len(items)} items")

        def render_items(items, subtitulo_func=None):
            html = ""
            for item in items:
                subtitulo = subtitulo_func(item) if subtitulo_func else ""
                html += f"<div class='item-card'><div class='item-header'>"
                html += f"<h3 class='item-name'>{item.get('nombre', '')}</h3>"
                html += f"<span class='item-price'>{self.formatear_precio(item.get('pvp'))}</span></div>"
                html += f"<p class='item-description'>{subtitulo}{item.get('descripcion','')}</p></div>"
            return html

        def subtitulo_cerveza(item):
            estilo = item.get("estilo_id")
            return f"<em style='font-size: 0.85em; color: #666;'>Estilo: {estilo}</em><br>" if estilo else ""

        # Generar secciones de comida - solo mostrar categorías que tengan items
        secciones_comida = ""
        for cat in categorias_comida:
            if categorias_comida[cat]:  # Solo si tiene elementos
                icon = self.ICONOS.get(cat, "🍽️")
                titulo = cat.upper().replace('_', ' ')
                bloque = render_items(categorias_comida[cat])
                secciones_comida += f"<div class='category-section'><h3 class='category-title'>{icon} {titulo}</h3><div class='items-grid'>{bloque}</div></div>"

        # Generar secciones de bebida - solo mostrar tipos que tengan items
        secciones_bebida = ""
        orden_bebidas = ["cerveza", "refrescos", "vinos", "cafes", "whiskey", "ron", "vodka", "ginebra", "destilados"]
        
        for tipo in orden_bebidas:
            if bebidas_por_tipo[tipo]:  # Solo si tiene elementos
                icon = self.ICONOS.get(tipo, "🥤")
                titulo = tipo.upper()
                subt = subtitulo_cerveza if tipo == "cerveza" else None
                bloque = render_items(bebidas_por_tipo[tipo], subt)
                secciones_bebida += f"<div class='category-section'><h3 class='category-title'>{icon} {titulo}</h3><div class='items-grid'>{bloque}</div></div>"

        # Cargar y procesar template
        try:
            # Buscar el archivo de template (puede tener nombres diferentes)
            template_files = ["carta_template.html", "template.html", "IndexCarta_template.html"]
            template_content = None
            
            for template_file in template_files:
                try:
                    with open(template_file, "r", encoding="utf-8") as tpl:
                        template_content = tpl.read()
                        print(f"✓ Template encontrado: {template_file}")
                        break
                except FileNotFoundError:
                    continue
            
            if not template_content:
                # Si no encuentra template, usar el HTML directo que me proporcionaste
                template_content = '''<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ nombre_restaurante }}</title>
    <link rel="stylesheet" href="css/cartaStyle.css">
</head>
<body>
    <div class="menu-container">
        <div class="header">
            <div class="fecha">{{ fecha }}</div>
            <h1>{{ nombre_restaurante }}</h1>
            <p>Carta de Comidas y Bebidas</p>
        </div>
        <div class="menu-content">
            <div class="section">
                <h2 class="section-title">Comida</h2>
                {{ secciones_comida }}
            </div>
            <div class="section">
                <h2 class="section-title">Bebidas</h2>
                {{ secciones_bebida }}
            </div>
        </div>
        <div class="footer">Gracias por su visita</div>
    </div>
</body>
</html>'''
                print("⚠ Usando template HTML incorporado")
            
            html = template_content
            html = html.replace("{{ nombre_restaurante }}", nombre_restaurante)
            html = html.replace("{{ fecha }}", datetime.now().strftime('%d/%m/%Y'))
            html = html.replace("{{ secciones_comida }}", secciones_comida)
            html = html.replace("{{ secciones_bebida }}", secciones_bebida)
                
        except Exception as e:
            print(f"Error procesando template: {e}")
            return f"<html><body><h1>Error procesando template: {e}</h1></body></html>"

        return html