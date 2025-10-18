#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para traducir las páginas restantes: catalogo.html, tallas.html, terminos.html
"""

import re
import os

# Diccionario completo de traducciones ES -> EN
TRANSLATIONS = {
    # Header común
    "Catálogo": "Catalog",
    "Mystery Box": "Mystery Box",
    "Suscripciones": "Subscriptions",
    
    # Catalogo.html
    "Catálogo Completo": "Complete Catalog",
    "Explora Nuestro Catálogo": "Explore Our Catalog",
    "Más de 500 camisetas de fútbol disponibles. Equipaciones de los mejores clubes y selecciones. Pedidos bajo demanda.": "Over 500 football jerseys available. Kits from the best clubs and national teams. Orders on demand.",
    "Pedidos Bajo Demanda": "Orders on Demand",
    "Todas las camisetas están sujetas a disponibilidad y stock.": "All jerseys are subject to availability and stock.",
    "Los pedidos se gestionan de forma personalizada a través de Telegram.": "Orders are handled personally through Telegram.",
    "Contáctanos para consultar disponibilidad y precios.": "Contact us to check availability and prices.",
    "Contactar por Telegram": "Contact via Telegram",
    "LaLiga": "LaLiga",
    "Premier League": "Premier League",
    "Serie A": "Serie A",
    "Bundesliga": "Bundesliga",
    "Ligue 1": "Ligue 1",
    "Selecciones": "National Teams",
    "Buscar equipo...": "Search team...",
    "Mostrando": "Showing",
    "equipos": "teams",
    "No se encontraron resultados": "No results found",
    "Prueba con otro término de búsqueda o selecciona otra liga": "Try another search term or select another league",
    "¿No encuentras lo que buscas?": "Can't find what you're looking for?",
    "Pregúntanos por Telegram": "Ask us on Telegram",
    "Local": "Home",
    "Visitante": "Away",
    "Consultar Disponibilidad": "Check Availability",
    
    # Tallas.html
    "Guía de Tallas": "Size Guide",
    "Encuentra tu talla perfecta. Todas las medidas están en centímetros (CM) y son aproximadas, pueden variar ±2-3cm según el fabricante.": "Find your perfect size. All measurements are in centimeters (CM) and are approximate, may vary ±2-3cm depending on the manufacturer.",
    "Consejos para elegir tu talla": "Tips for choosing your size",
    "Mide con precisión:": "Measure accurately:",
    "Usa una cinta métrica flexible y mide sobre ropa ligera": "Use a flexible measuring tape and measure over light clothing",
    "Anchura de pecho:": "Chest width:",
    "Rodea el pecho por la parte más ancha, pasando por debajo de las axilas": "Go around the chest at the widest part, passing under the armpits",
    "Longitud:": "Length:",
    "Desde el hombro hasta el final de la prenda": "From shoulder to end of garment",
    "Altura:": "Height:",
    "Tu altura total sin zapatos": "Your total height without shoes",
    "Entre tallas:": "Between sizes:",
    "Si estás entre dos tallas, elige la mayor para un ajuste más cómodo": "If you're between two sizes, choose the larger one for a more comfortable fit",
    "¿Dudas?": "Questions?",
    "Contáctanos por WhatsApp antes de realizar tu pedido": "Contact us on WhatsApp before placing your order",
    "General": "General",
    "Player Version": "Player Version",
    "Niños": "Kids",
    "Chandals": "Tracksuits",
    "GENERAL": "GENERAL",
    "Guía de tallas estándar para camisetas de aficionado. Ajuste clásico y cómodo para uso diario.": "Standard size guide for fan jerseys. Classic and comfortable fit for daily use.",
    "TALLA": "SIZE",
    "ANCHURA PECHO (CM)": "CHEST WIDTH (CM)",
    "LONGITUD (CM)": "LENGTH (CM)",
    "ALTURA (CM)": "HEIGHT (CM)",
    "PESO (KG)": "WEIGHT (KG)",
    "*Este tamaño se mide a mano, puede haber un error de 2-3 cm, solo como referencia. La altura y el peso es una orientación.": "*This size is hand-measured, there may be a 2-3 cm error, for reference only. Height and weight are guidance.",
    "PLAYER VERSION": "PLAYER VERSION",
    "Versión profesional con ajuste ceñido y tecnología deportiva avanzada. Corte slim fit como usan los jugadores en el campo.": "Professional version with tight fit and advanced sports technology. Slim fit cut as used by players on the field.",
    "Diferencias Player Version": "Player Version Differences",
    "Ajuste más ceñido:": "Tighter fit:",
    "Corte slim fit profesional": "Professional slim fit cut",
    "Materiales premium:": "Premium materials:",
    "Tecnología Dri-FIT o similar": "Dri-FIT technology or similar",
    "Peso ligero:": "Lightweight:",
    "Diseño ultraligero para máximo rendimiento": "Ultralight design for maximum performance",
    "Recomendación:": "Recommendation:",
    "Si prefieres un ajuste más holgado, elige una talla más grande": "If you prefer a looser fit, choose a larger size",
    "NIÑOS": "KIDS",
    "Tallas especiales para los pequeños aficionados. Basadas en edad y medidas corporales adaptadas.": "Special sizes for young fans. Based on age and adapted body measurements.",
    "EDAD": "AGE",
    "ANCHO (CM)": "WIDTH (CM)",
    "Consejos para tallas infantiles": "Tips for children's sizes",
    "Crecimiento:": "Growth:",
    "Los niños crecen rápido, considera una talla mayor si está entre dos medidas": "Children grow fast, consider a larger size if between two measurements",
    "Edad orientativa:": "Guideline age:",
    "Usa la edad como guía, pero prioriza las medidas reales del niño": "Use age as a guide, but prioritize the child's actual measurements",
    "Comodidad:": "Comfort:",
    "Asegúrate de que la camiseta permita libertad de movimiento": "Make sure the jersey allows freedom of movement",
    "Ancho de pecho:": "Chest width:",
    "Mide el contorno del pecho del niño para mayor precisión": "Measure the child's chest circumference for greater accuracy",
    "CHANDALS Y CONJUNTOS": "TRACKSUITS AND SETS",
    "Guía de tallas para conjuntos deportivos completos, chandals de entrenamiento y ropa técnica.": "Size guide for complete sports sets, training tracksuits and technical clothing.",
    "LARGO (CM)": "LENGTH (CM)",
    "CIRCUNFERENCIA DEL PECHO (CM)": "CHEST CIRCUMFERENCE (CM)",
    "Características de Chandals": "Tracksuit Features",
    "Conjunto completo:": "Complete set:",
    "Incluye chaqueta y pantalón a juego": "Includes matching jacket and pants",
    "Ajuste deportivo:": "Athletic fit:",
    "Diseñado para entrenamiento y uso casual": "Designed for training and casual use",
    "Circunferencia:": "Circumference:",
    "Mide alrededor del pecho pasando por debajo de las axilas": "Measure around the chest passing under the armpits",
    "Largo:": "Length:",
    "Desde el cuello hasta el final de la chaqueta": "From neck to end of jacket",
    "¿Necesitas ayuda con tu talla?": "Need help with your size?",
    "Nuestro equipo está listo para ayudarte a encontrar la talla perfecta. ¡Contáctanos!": "Our team is ready to help you find the perfect size. Contact us!",
    "Consultar por WhatsApp": "Consult on WhatsApp",
    "Ver Catálogo": "View Catalog",
    
    # Términos.html
    "Términos y Condiciones": "Terms and Conditions",
    "kickverse.es - Tu tienda de camisetas de fútbol personalizadas": "kickverse.es - Your custom football jersey store",
    "Oferta especial primera compra hasta 01/11 - Código:": "Special first purchase offer until 01/11 - Code:",
    "INFORMACIÓN GENERAL": "GENERAL INFORMATION",
    "SECCIÓN": "SECTION",
    "TÉRMINOS DE LA TIENDA EN LÍNEA": "ONLINE STORE TERMS",
    "CONDICIONES GENERALES": "GENERAL CONDITIONS",
    "EXACTITUD Y ACTUALIDAD DE LA INFORMACIÓN": "ACCURACY AND TIMELINESS OF INFORMATION",
    "MODIFICACIONES AL SERVICIO Y PRECIOS": "SERVICE AND PRICE MODIFICATIONS",
    "NATURALEZA DEL SERVICIO Y PRODUCTOS": "NATURE OF SERVICE AND PRODUCTS",
    "FACTURACIÓN E INFORMACIÓN DE CUENTA": "BILLING AND ACCOUNT INFORMATION",
    "HERRAMIENTAS OPCIONALES": "OPTIONAL TOOLS",
    "ENLACES DE TERCERAS PARTES": "THIRD PARTY LINKS",
    "COMENTARIOS DE USUARIO": "USER COMMENTS",
    "INFORMACIÓN PERSONAL": "PERSONAL INFORMATION",
    "ERRORES Y OMISIONES": "ERRORS AND OMISSIONS",
    "USOS PROHIBIDOS": "PROHIBITED USES",
    "EXCLUSIÓN DE GARANTÍAS Y LIMITACIÓN DE RESPONSABILIDAD": "DISCLAIMER OF WARRANTIES AND LIMITATION OF LIABILITY",
    "INDEMNIZACIÓN Y PROTECCIÓN LEGAL": "INDEMNIFICATION AND LEGAL PROTECTION",
    "DIVISIBILIDAD": "SEVERABILITY",
    "RESCISIÓN": "TERMINATION",
    "ACUERDO COMPLETO": "ENTIRE AGREEMENT",
    "LEY APLICABLE": "APPLICABLE LAW",
    "CAMBIOS EN LOS TÉRMINOS": "CHANGES TO TERMS",
    "ENVÍOS Y ENTREGAS": "SHIPPING AND DELIVERIES",
    "POLÍTICA DE DEVOLUCIONES Y RECLAMACIONES": "RETURNS AND CLAIMS POLICY",
    "DECLARACIÓN SOBRE PROPIEDAD INTELECTUAL Y MARCAS": "INTELLECTUAL PROPERTY AND TRADEMARKS STATEMENT",
    "¿Tienes preguntas sobre nuestros términos?": "Have questions about our terms?",
    "Estamos aquí para ayudarte. Contacta con nosotros por cualquiera de estos medios:": "We're here to help. Contact us through any of these means:",
    
    # Footer común
    "Productos": "Products",
    "Legal": "Legal",
    "Contacto": "Contact",
    "Política de Privacidad": "Privacy Policy",
    "Política de Envíos": "Shipping Policy",
    "Devoluciones": "Returns",
    "Telegram": "Telegram",
    "Email": "Email",
    "Todos los derechos reservados.": "All rights reserved.",
    "Tu tienda de camisetas de fútbol con la mejor calidad y precio. Catálogo completo, Mystery Boxes y suscripciones mensuales.": "Your football jersey store with the best quality and price. Complete catalog, Mystery Boxes and monthly subscriptions.",
}

def wrap_with_data_lang(spanish_text, english_text):
    """Envuelve textos en spans con atributos data-lang"""
    return f'<span data-lang="es">{spanish_text}</span><span data-lang="en">{english_text}</span>'

def translate_html_content(content, translations):
    """Aplica traducciones al contenido HTML"""
    
    for spanish, english in translations.items():
        # Escapar caracteres especiales para regex
        spanish_escaped = re.escape(spanish)
        
        # Patrón 1: Texto dentro de tags simple (más permisivo)
        # Busca >texto< y lo reemplaza si no tiene ya data-lang
        pattern1 = f'>({spanish_escaped})<'
        if re.search(pattern1, content):
            # Verificar que no tenga ya data-lang antes de reemplazar
            matches = list(re.finditer(pattern1, content))
            for match in reversed(matches):  # Invertido para no afectar posiciones
                start = match.start()
                # Mirar atrás 50 caracteres para ver si hay data-lang
                context_before = content[max(0, start-50):start]
                if 'data-lang=' not in context_before or context_before.rfind('<') > context_before.rfind('data-lang='):
                    # Reemplazar solo esta ocurrencia
                    content = content[:match.start()] + f'>{wrap_with_data_lang(spanish, english)}<' + content[match.end():]
        
        # Patrón 2: Placeholder en inputs
        pattern2 = f'placeholder="({spanish_escaped})"'
        if 'data-placeholder-en' not in content or spanish not in content:
            content = re.sub(pattern2, f'placeholder="{spanish}" data-placeholder-en="{english}"', content)
    
    return content

def add_lang_switcher_to_header(content):
    """Añade selector de idioma al header si no existe"""
    
    # Verificar si ya existe lang-switcher
    if 'lang-switcher' in content:
        return content
    
    # Buscar el nav del header para insertar el selector después
    lang_switcher = '''
            <div class="lang-switcher">
                <button class="lang-btn active" data-lang="es" aria-label="Español" aria-pressed="true">ES</button>
                <button class="lang-btn" data-lang="en" aria-label="English" aria-pressed="false">EN</button>
            </div>'''
    
    # Insertar antes del cierre de header-nav o header-actions
    if '<div class="header-actions">' in content:
        content = content.replace('<div class="header-actions">', lang_switcher + '\n            <div class="header-actions">')
    elif '</nav>' in content and 'header-nav' in content:
        content = content.replace('</nav>', '</nav>\n' + lang_switcher)
    
    return content

def add_lang_script(content):
    """Añade script lang.js si no existe"""
    
    if 'lang.js' in content:
        return content
    
    # Añadir antes del cierre de </body>
    script_tag = '    <script src="./js/lang.js"></script>\n'
    content = content.replace('</body>', script_tag + '</body>')
    
    return content

def process_file(file_path):
    """Procesa un archivo HTML completo"""
    
    print(f"\n📄 Procesando: {file_path}")
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # 1. Añadir selector de idioma al header
        content = add_lang_switcher_to_header(content)
        
        # 2. Aplicar traducciones
        content = translate_html_content(content, TRANSLATIONS)
        
        # 3. Añadir script lang.js
        content = add_lang_script(content)
        
        # Guardar archivo
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        # Contar traducciones añadidas
        es_count_before = original_content.count('data-lang="es"')
        es_count_after = content.count('data-lang="es"')
        new_translations = es_count_after - es_count_before
        
        print(f"✅ {file_path}")
        print(f"   📊 {new_translations} nuevas traducciones añadidas")
        print(f"   📊 Total: {es_count_after} pares de traducción")
        
        return True
        
    except Exception as e:
        print(f"❌ Error procesando {file_path}: {e}")
        return False

def main():
    """Función principal"""
    
    print("=" * 60)
    print("🌐 TRADUCTOR AUTOMÁTICO - PÁGINAS RESTANTES")
    print("=" * 60)
    
    # Archivos a procesar
    files = [
        'catalogo.html',
        'tallas.html',
        'terminos.html'
    ]
    
    success_count = 0
    total_files = len(files)
    
    for file in files:
        if os.path.exists(file):
            if process_file(file):
                success_count += 1
        else:
            print(f"⚠️  Archivo no encontrado: {file}")
    
    print("\n" + "=" * 60)
    print(f"✅ COMPLETADO: {success_count}/{total_files} archivos procesados")
    print("=" * 60)
    
    print("\n📋 Próximos pasos:")
    print("1. Prueba el selector de idioma en cada página")
    print("2. Verifica que todas las secciones cambian correctamente")
    print("3. Revisa la guía de tallas y términos")
    print("4. Commit: git add -A && git commit -m 'feat: Add multilingual support to catalog, sizes and terms pages'")

if __name__ == "__main__":
    main()
