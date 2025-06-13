import json
from datetime import datetime
from jinja2 import Template
from collections import defaultdict

class CartaRenderer:
    ICONOS = {
        "entrantes": "🥟", "vegetariano": "🥦", "vegano": "🌱",
        "entre_panes": "🍔", "mar": "🐟", "carnes": "🥩",
        "arroces_pastas": "🍝", "postres": "🍰",
        "cerveza": "🍺", "vinos": "🍷", "refrescos": "🧃",
"cafes": "☕", "destilados": "🍸"
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
        return comidas

    def obtener_bebidas(self):
        self.cursor.execute('SELECT * FROM bebida')
        columns = [col[0] for col in self.cursor.description]
        rows = self.cursor.fetchall()
        bebidas = [dict(zip(columns, row)) for row in rows]
        return bebidas

    def obtener_estilos(self):
        self.cursor.execute('SELECT * FROM estilo')
        columns = [col[0] for col in self.cursor.description]
        rows = self.cursor.fetchall()
        estilos = [dict(zip(columns, row)) for row in rows]
        return estilos

    def obtener_cervezas_por_estilo(self):
        query = """
        SELECT 
            b.id, b.nombre, b.grado_alcoholico, b.formato, b.pvp, 
            b.descripcion, b.lupulos, b.estilo_id,
            e.nombre as estilo_nombre, e.descripcion as estilo_descripcion,
            e.color, e.sabor, e.aroma, e.origen
        FROM bebida b
        LEFT JOIN estilo e ON b.estilo_id = e.id
        WHERE b.tipo_bebida = 'cerveza'
        ORDER BY e.nombre, b.nombre
        """
        
        self.cursor.execute(query)
        columns = [col[0] for col in self.cursor.description]
        rows = self.cursor.fetchall()
        cervezas = [dict(zip(columns, row)) for row in rows]
        
        cervezas_por_estilo = {}
        cervezas_sin_estilo = []
        
        for cerveza in cervezas:
            estilo_id = cerveza.get('estilo_id')
            estilo_nombre = cerveza.get('estilo_nombre')
            
            if estilo_id and estilo_nombre:
                if estilo_nombre not in cervezas_por_estilo:
                    cervezas_por_estilo[estilo_nombre] = {
                        'estilo_info': {
                            'id': estilo_id,
                            'nombre': estilo_nombre,
                            'descripcion': cerveza.get('estilo_descripcion'),
                            'color': cerveza.get('color'),
                            'sabor': cerveza.get('sabor'),
                            'aroma': cerveza.get('aroma'),
                            'origen': cerveza.get('origen')
                        },
                        'cervezas': []
                    }
                cervezas_por_estilo[estilo_nombre]['cervezas'].append(cerveza)
            else:
                cervezas_sin_estilo.append(cerveza)
        
        return cervezas_por_estilo, cervezas_sin_estilo

    def formatear_precio(self, pvp):
        return f"{pvp:.2f} €" if pvp else "-"

    def clasificar_destilado(self, bebida):
        descripcion = (bebida.get("descripcion") or "").lower()
        nombre = (bebida.get("nombre") or "").lower()
        texto_completo = f"{nombre} {descripcion}"
        
        if any(palabra in texto_completo for palabra in ["whiskey", "whisky", "bourbon"]):
            return "whiskey"
        elif any(palabra in texto_completo for palabra in ["ron", "rum"]):
            return "ron"
        elif any(palabra in texto_completo for palabra in ["vodka"]):
            return "vodka"
        elif any(palabra in texto_completo for palabra in ["ginebra", "gin"]):
            return "ginebra"
        else:
            return "destilados"

    def render_items(self, items):
        html = ""
        for item in items:
            ingredientes = ""
            if item.get('ingredientes'):
                ingredientes = f"<div class='ingredientes'><strong>Ingredientes:</strong> {item['ingredientes']}</div>"
            
            descripcion = ""
            if item.get('descripcion') and item['descripcion'].lower() != 'none':
                descripcion = f"<div class='descripcion'>{item['descripcion']}</div>"
            
            html += f"""
            <div class='item-card'>
                <div class='item-header'>
                    <h3 class='item-name'>{item.get('nombre', '')}</h3>
                    <span class='item-price'>{self.formatear_precio(item.get('pvp'))}</span>
                </div>
                {descripcion}
                {ingredientes}
                <button class='boton-comanda'>Añadir a la comanda</button>
                <hr class='item-divider'>
            </div>
            """
        return html

    def render_cervezas_por_estilo(self, cervezas_por_estilo, cervezas_sin_estilo):
        html = ""
        
        for estilo_nombre, data in sorted(cervezas_por_estilo.items()):
            estilo_info = data['estilo_info']
            cervezas = data['cervezas']
            
            estilo_descripcion = ""
            if estilo_info.get('descripcion'):
                estilo_descripcion = f"<div class='estilo-descripcion'>{estilo_info['descripcion']}</div>"
            
            caracteristicas = []
            if estilo_info.get('color'):
                caracteristicas.append(f"Color: {estilo_info['color']}")
            if estilo_info.get('sabor'):
                caracteristicas.append(f"Sabor: {estilo_info['sabor']}")
            if estilo_info.get('aroma'):
                caracteristicas.append(f"Aroma: {estilo_info['aroma']}")
            if estilo_info.get('origen'):
                caracteristicas.append(f"Origen: {estilo_info['origen']}")
            
            caracteristicas_html = ""
            if caracteristicas:
                caracteristicas_html = f"<div class='estilo-caracteristicas'>{' | '.join(caracteristicas)}</div>"
            
            html += f"""
            <div class='estilo-section'>
                <h4 class='estilo-title'>{estilo_nombre}</h4>
                {estilo_descripcion}
                {caracteristicas_html}
            """
            
            cervezas_html = ""
            for cerveza in cervezas:
                info_cerveza = []
                if cerveza.get('grado_alcoholico'):
                    info_cerveza.append(f"<strong>{cerveza['grado_alcoholico']}% vol.</strong>")
                if cerveza.get('formato'):
                    info_cerveza.append(f"Formato: {cerveza['formato']}")
                if cerveza.get('lupulos'):
                    info_cerveza.append(f"Lúpulos: {cerveza['lupulos']}")
                
                descripcion_completa = "<br>".join(info_cerveza)
                if cerveza.get('descripcion') and cerveza['descripcion'].lower() != 'none':
                    descripcion_completa += f"<br>{cerveza['descripcion']}"
                
                cervezas_html += f"""
                <div class='item-card cerveza-card'>
                    <div class='item-header'>
                        <h3 class='item-name'>{cerveza.get('nombre', '')}</h3>
                        <span class='item-price'>{self.formatear_precio(cerveza.get('pvp'))}</span>
                    </div>
                    {f"<div class='item-description'>{descripcion_completa}</div>" if descripcion_completa else ""}
                    <button class='boton-comanda'>Añadir a la comanda</button>
                    <hr class='item-divider'>
                </div>
                """
            
            html += cervezas_html + "</div>"

        if cervezas_sin_estilo:
            html += "<div class='estilo-section'><h4 class='estilo-title'>Otras Cervezas</h4>"
            
            for cerveza in cervezas_sin_estilo:
                info_cerveza = []
                if cerveza.get('grado_alcoholico'):
                    info_cerveza.append(f"<strong>{cerveza['grado_alcoholico']}% vol.</strong>")
                if cerveza.get('formato'):
                    info_cerveza.append(f"Formato: {cerveza['formato']}")
                
                descripcion_completa = "<br>".join(info_cerveza)
                if cerveza.get('descripcion') and cerveza['descripcion'].lower() != 'none':
                    descripcion_completa += f"<br>{cerveza['descripcion']}"
                
                html += f"""
                <div class='item-card cerveza-card'>
                    <div class='item-header'>
                        <h3 class='item-name'>{cerveza.get('nombre', '')}</h3>
                        <span class='item-price'>{self.formatear_precio(cerveza.get('pvp'))}</span>
                    </div>
                    {f"<div class='item-description'>{descripcion_completa}</div>" if descripcion_completa else ""}
                    <button class='boton-comanda'>Añadir a la comanda</button>
                    <hr class='item-divider'>
                </div>
                """
            
            html += "</div>"
    
        return html

    def render_carta(self, nombre_restaurante="Restaurante El Cañaveral"):
        if not self.conectar():
            return "<html><body><h1>Error de conexión</h1></body></html>"

        comidas_raw = self.obtener_comidas()
        bebidas_raw = self.obtener_bebidas()
        cervezas_por_estilo, cervezas_sin_estilo = self.obtener_cervezas_por_estilo()

        categorias_comida = {
            "entrantes": [], "vegetariano": [], "vegano": [], "entre_panes": [],
            "mar": [], "carnes": [], "arroces_pastas": [], "postres": []
        }

        for c in comidas_raw:
            categoria_raw = c.get("categoria", "")
            cats = []
            
            if isinstance(categoria_raw, str) and categoria_raw:
                try:
                    cats = json.loads(categoria_raw)
                except (json.JSONDecodeError, TypeError):
                    cats = [categoria_raw]
            elif isinstance(categoria_raw, list):
                cats = categoria_raw
            
            for cat in cats:
                cat_original = str(cat).strip()
                cat_normalizada = cat_original.lower().replace(" ", "_")
                
                mapeo_categorias = {
                    "arroz": "arroces_pastas", "pasta": "arroces_pastas", 
                    "arroces": "arroces_pastas", "pastas": "arroces_pastas",
                    "arroces_y_pastas": "arroces_pastas", "arroces_pastas": "arroces_pastas",
                    "pescado": "mar", "mariscos": "mar",
                    "carne": "carnes", "entrante": "entrantes", "postre": "postres"
                }
                
                cat_final = mapeo_categorias.get(cat_normalizada, cat_normalizada)
                
                if cat_final in categorias_comida and c not in categorias_comida[cat_final]:
                    categorias_comida[cat_final].append(c)

        bebidas_por_tipo = {
            "refrescos": [], "vinos": [], "cafes": [],
                "refrescos": [], "vinos": [], "cafes": [], "destilados": []
        }

        for b in bebidas_raw:
            tipo = (b.get("tipo_bebida") or "").strip().lower()
            
            if tipo == "cerveza":
                continue
                
            if tipo == "destilados":
                categoria_destilado = self.clasificar_destilado(b)
                bebidas_por_tipo["destilados"].append(b)
            elif tipo in bebidas_por_tipo:
                bebidas_por_tipo[tipo].append(b)
            else:
                mapeo_tipos = {
                    "vino": "vinos", "refresco": "refrescos",
                    "cafe": "cafes", "café": "cafes", "gaseosa": "refrescos"
                }
                tipo_mapeado = mapeo_tipos.get(tipo)
                if tipo_mapeado and tipo_mapeado in bebidas_por_tipo:
                    bebidas_por_tipo[tipo_mapeado].append(b)

        # Generar secciones de contenido
        secciones_comida = ""
        for cat in categorias_comida:
            if categorias_comida[cat]:
                icon = self.ICONOS.get(cat, "🍽️")
                titulo = cat.upper().replace('_', ' ')
                if cat == 'arroces_pastas':
                    titulo = 'ARROCES Y PASTA'
                bloque = self.render_items(categorias_comida[cat])
                secciones_comida += f"""
                <div class='category-section' id='{cat}'>
                    <h3 class='category-title'>{icon} {titulo}</h3>
                    <div class='items-grid'>{bloque}</div>
                </div>
                """

        seccion_cervezas = ""
        if cervezas_por_estilo or cervezas_sin_estilo:
            cervezas_html = self.render_cervezas_por_estilo(cervezas_por_estilo, cervezas_sin_estilo)
            seccion_cervezas = f"""
            <div class='category-section' id='cervezas'>
                <h3 class='category-title'>🍺 CERVEZAS</h3>
                {cervezas_html}
            </div>
            """

        secciones_otras_bebidas = ""
        orden_bebidas = ["refrescos", "vinos", "cafes", "destilados"]
        
        for tipo in orden_bebidas:
            if bebidas_por_tipo[tipo]:
                icon = self.ICONOS.get(tipo, "🥤")
                titulo = tipo.upper()
                if tipo == 'cafes':
                    titulo = 'CAFÉS'
                bloque = self.render_items(bebidas_por_tipo[tipo])
                secciones_otras_bebidas += f"""
                <div class='category-section' id='{tipo}'>
                    <h3 class='category-title'>{icon} {titulo}</h3>
                    <div class='items-grid'>{bloque}</div>
                </div>
                """

        # Generar índice interactivo
        indice_interactivo = """
        <div class="indice-container">
            <h3 class="indice-title">Índice</h3>
            <ul class="indice-list">
        """
        
        # Añadir enlaces para las secciones de comida
        categorias_mostradas = [cat for cat in categorias_comida if categorias_comida[cat]]
        for cat in categorias_mostradas:
            nombre_seccion = cat.replace('_', ' ').title()
            if cat == 'arroces_pastas':
                nombre_seccion = 'Arroces y Pasta'
            indice_interactivo += f"""
                <li><a href="#{cat}">{self.ICONOS.get(cat, "🍽️")} {nombre_seccion}</a></li>
            """
        
        # Añadir enlace para cervezas si hay
        if cervezas_por_estilo or cervezas_sin_estilo:
            indice_interactivo += """
                <li><a href="#cervezas">🍺 Cervezas</a></li>
            """
        
        # Añadir enlaces para otras bebidas
        tipos_bebida_mostrados = [tipo for tipo in bebidas_por_tipo if bebidas_por_tipo[tipo]]
        for tipo in tipos_bebida_mostrados:
            nombre_seccion = tipo.title()
            if tipo == 'cafes':
                nombre_seccion = 'Cafés'
            indice_interactivo += f"""
                <li><a href="#{tipo}">{self.ICONOS.get(tipo, "🥤")} {nombre_seccion}</a></li>
            """
        
        indice_interactivo += """
            </ul>
        </div>
        """

        try:
            template_files = ["carta_template.html", "template.html", "IndexCarta_template.html"]
            template_content = None
            
            for template_file in template_files:
                try:
                    with open(template_file, "r", encoding="utf-8") as tpl:
                        template_content = tpl.read()
                        break
                except FileNotFoundError:
                    continue
            
            if template_content:
                template_content = template_content.replace(
                    '<div class="menu-content">',
                    f'<div class="menu-content">{indice_interactivo}'
                )
                
                template = Template(template_content)
                context = {
                    "nombre_restaurante": nombre_restaurante,
                    "fecha": datetime.now().strftime('%d/%m/%Y'),
                    "comidas": {k: v for k, v in categorias_comida.items() if v},
                    "bebidas": {k: v for k, v in bebidas_por_tipo.items() if v},
                    "secciones_comida": secciones_comida,
                    "seccion_cervezas": seccion_cervezas,
                    "secciones_otras_bebidas": secciones_otras_bebidas
                }
                html = template.render(context)
            else:
                template_content = '''<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ nombre_restaurante }}</title>
    <link rel="stylesheet" href="css/cartaStyle.css">
    <style>
        h1{
        text-align:center;
        }
        .estilo-section {
            margin-bottom: 2rem;
            border-left: 4px solid #f39c12;
            padding-left: 1rem;
        }
        .estilo-title {
            color: #f39c12;
            font-size: 1.3em;
            margin-bottom: 0.5rem;
            
        }
        .estilo-descripcion {
            font-style: italic;
            color: #666;
            margin-bottom: 0.5rem;
            font-size: 1.0em;
        }
        .estilo-caracteristicas {
            font-size: 1.0em;
            color: #666;
            margin-bottom: 1rem;
        }
        .cerveza-card {
            border-left: 2px solid #f39c12;
        }
        .indice-container {
            background-color: #f3e6b7;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .indice-title {
            margin-top: 0;
            color: #333;
            font-size: 1.2em;
        }
        .indice-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: #273746;
        }
        .indice-list li {
            margin: 0;
            color: #273746;
        }
        .indice-list a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f1c40f
            color: #273746;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.2s;
        }
        .indice-list a:hover {
            background-color: #f1c40f;
            color: #212529;
        }
        .section-title{
        text-align:center;
        }
        .descripcion{
        font-size: 1.0em;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <div class="header">
            <h1>{{ nombre_restaurante }}</h1>
        </div>
        <div class="menu-content">
            {indice_interactivo}
            <div class="section">
                <h2 class="section-title">Comida</h2>
                {{ secciones_comida }}
            </div>
            <div class="section">
                <h2 class="section-title">Bebidas</h2>
                {{ seccion_cervezas }}
                {{ secciones_otras_bebidas }}
            </div>
        </div>
        <div class="footer">Gracias por su visita</div>
    </div>
</body>
</html>'''
                
                html = template_content
                html = html.replace("{{ nombre_restaurante }}", nombre_restaurante)
                html = html.replace("{{ fecha }}", datetime.now().strftime('%d/%m/%Y'))
                html = html.replace("{{ secciones_comida }}", secciones_comida)
                html = html.replace("{{ seccion_cervezas }}", seccion_cervezas)
                html = html.replace("{{ secciones_otras_bebidas }}", secciones_otras_bebidas)
                html = html.replace("{indice_interactivo}", indice_interactivo)
                
        except Exception as e:
            print(f"Error procesando template: {e}")
            return f"<html><body><h1>Error procesando template: {e}</h1></body></html>"

        return html