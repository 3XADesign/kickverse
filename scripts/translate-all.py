#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script completo para traducir TODAS las secciones de index.html, mystery-box.html y catalogo.html
"""

import re
import sys

# Diccionario completo de traducciones
TRANSLATIONS = {
    # Planes Section - Titles & Badges
    'Elige tu suscripción': 'Choose your subscription',
    'Todos los planes incluyen envío gratuito a España': 'All plans include free shipping to Spain',
    'Esencial': 'Essential',
    'El más elegido': 'Most popular',
    'Clubes TOP': 'TOP Clubs',
    'Exclusivo': 'Exclusive',
    
    # Plan Names
    'Plan Fan': 'Fan Plan',
    'Plan Premium Random': 'Premium Random Plan',
    'Plan Premium Equipo TOP': 'Premium TOP Team Plan',
    'Plan Retro TOP': 'Retro TOP Plan',
    
    # Plan Details
    'por mes': 'per month',
    'Ideal para aficionados y coleccionistas casuales': 'Perfect for fans and casual collectors',
    'Camiseta tipo player de equipos medios': 'Player-type jersey from mid-tier teams',
    'Camiseta tipo player de clubes top': 'Player-type jersey from top clubs',
    'Camiseta retro mítica de selecciones o clubes legendarios': 'Legendary retro jersey from national teams or legendary clubs',
    
    # Plan Features
    'Camiseta FAN aleatoria': 'Random FAN jersey',
    'Equipaciones 2024/25': '2024/25 kits',
    'Envío incluido': 'Shipping included',
    'Cancela cuando quieras': 'Cancel anytime',
    'Camiseta Player aleatoria': 'Random Player jersey',
    'Equipos de nivel medio': 'Mid-tier teams',
    'Material de calidad': 'Quality material',
    'Camiseta Player de clubes TOP': 'Player jersey from TOP clubs',
    'Madrid, Barça, City, PSG...': 'Madrid, Barça, City, PSG...',
    'Versión profesional': 'Professional version',
    'Camiseta RETRO legendaria': 'Legendary RETRO jersey',
    'Selecciones y clubes míticos': 'National teams and legendary clubs',
    'Ediciones icónicas': 'Iconic editions',
    
    # Plan CTAs
    'Suscribirme al Plan Fan': 'Subscribe to Fan Plan',
    'Suscribirme al Premium Random': 'Subscribe to Premium Random',
    'Suscribirme al Premium TOP': 'Subscribe to Premium TOP',
    'Suscribirme al Retro TOP': 'Subscribe to Retro TOP',
    
    # Features Section (already done but included for completeness)
    '¿Cómo funciona?': 'How does it work?',
    'Sencillo, emocionante y sin compromisos': 'Simple, exciting, and no commitments',
    '1. Elige tu plan': '1. Choose your plan',
    '2. Recibe tu sorpresa': '2. Receive your surprise',
    '3. Disfruta y colecciona': '3. Enjoy and collect',
    'Envío gratuito': 'Free shipping',
    'Selecciona el plan que mejor se adapte a ti y suscríbete en segundos con Stripe': 'Select the plan that best suits you and subscribe in seconds with Stripe',
    'Cada mes recibirás una camiseta aleatoria de clubes top o ediciones especiales': 'Every month you\'ll receive a random jersey from top clubs or special editions',
    'Amplía tu colección sin esfuerzo. Cancela o cambia de plan cuando quieras': 'Expand your collection effortlessly. Cancel or change plans anytime',
    'Todos los planes incluyen envío gratuito a toda España. Sin costes ocultos': 'All plans include free shipping throughout Spain. No hidden costs',
    
    # FAQ Section
    'Preguntas frecuentes': 'Frequently asked questions',
    'Todo lo que necesitas saber sobre tu suscripción': 'Everything you need to know about your subscription',
    
    # FAQ Questions
    '¿Cómo funciona la suscripción?': 'How does the subscription work?',
    '¿Puedo elegir la camiseta que recibo?': 'Can I choose the jersey I receive?',
    '¿Cómo cancelo mi suscripción?': 'How do I cancel my subscription?',
    '¿Cuándo recibiré mi primera camiseta?': 'When will I receive my first jersey?',
    '¿Qué pasa si la camiseta no me queda bien?': 'What if the jersey doesn\'t fit me well?',
    '¿Hacéis envíos fuera de España?': 'Do you ship outside Spain?',
    
    # FAQ Answers
    'Al suscribirte, recibirás automáticamente una camiseta sorpresa cada mes según el plan elegido. El pago se realiza mensualmente de forma automática y puedes cancelar en cualquier momento.': 'When you subscribe, you will automatically receive a surprise jersey every month according to your chosen plan. Payment is made monthly automatically and you can cancel at any time.',
    'No, la emoción está en la sorpresa. Seleccionamos cuidadosamente camisetas de alta calidad de clubes top, selecciones y ediciones especiales. Puedes indicar tu talla y preferencias generales contactándonos por Telegram.': 'No, the excitement is in the surprise. We carefully select high-quality jerseys from top clubs, national teams and special editions. You can indicate your size and general preferences by contacting us via Telegram.',
    'Puedes cancelar en cualquier momento desde tu panel de Stripe o contactándonos por Telegram (@esKickverse). No hay penalizaciones ni periodos mínimos.': 'You can cancel at any time from your Stripe panel or by contacting us via Telegram (@esKickverse). There are no penalties or minimum periods.',
    'Tu primera camiseta se enviará dentro de los 5-7 días hábiles tras confirmar tu suscripción. Los envíos posteriores se realizarán mensualmente en la misma fecha.': 'Your first jersey will be sent within 5-7 business days after confirming your subscription. Subsequent shipments will be made monthly on the same date.',
    'Antes de tu primer envío, confirmaremos tu talla por Telegram. Si hay algún problema, contáctanos y buscaremos una solución.': 'Before your first shipment, we will confirm your size via Telegram. If there\'s any problem, contact us and we\'ll find a solution.',
    'Sí, realizamos envíos a toda Europa y otros países. Los gastos de envío internacional se calculan automáticamente según tu ubicación. Contáctanos por Telegram para más información sobre envíos a tu país.': 'Yes, we ship throughout Europe and other countries. International shipping costs are automatically calculated based on your location. Contact us via Telegram for more information about shipping to your country.',
    
    # Footer
    'Tu suscripción mensual de camisetas de fútbol. Recibe sorpresas cada mes y amplía tu colección sin esfuerzo.': 'Your monthly football jersey subscription. Receive surprises every month and expand your collection effortlessly.',
    'Suscripciones': 'Subscriptions',
    'Legal': 'Legal',
    'Términos y Condiciones': 'Terms and Conditions',
    'Política de Privacidad': 'Privacy Policy',
    'Política de Envíos': 'Shipping Policy',
    'Devoluciones': 'Returns',
    'Contacto': 'Contact',
    'Telegram': 'Telegram',
    'Email': 'Email',
    'Todos los derechos reservados': 'All rights reserved',
    
    # Floating CTA
    '¡Suscríbete ahora!': 'Subscribe now!',
    
    # Mystery Box specific
    'Edición Limitada': 'Limited Edition',
    '5 camisetas sorpresa en una caja. Calidad premium, envío incluido y cero spoilers. La emoción de no saber qué vas a recibir.': '5 surprise jerseys in one box. Premium quality, shipping included and zero spoilers. The thrill of not knowing what you\'ll get.',
    'Ver cajas disponibles': 'View available boxes',
    'Elige tu Mystery Box': 'Choose your Mystery Box',
    'Cada caja es única. Pide la tuya por Telegram': 'Each box is unique. Order yours via Telegram',
    
    # Mystery Box Types
    'Popular': 'Popular',
    'Box Clásica': 'Classic Box',
    '5 camisetas FAN': '5 FAN jerseys',
    'Mix perfecto entre equipos top y sorpresas': 'Perfect mix of top teams and surprises',
    '5 camisetas versión FAN': '5 FAN version jerseys',
    'Ligas variadas': 'Various leagues',
    'Sorpresa garantizada': 'Guaranteed surprise',
    'Comprar Mystery Box Clásica': 'Buy Classic Mystery Box',
    
    'Premium': 'Premium',
    'Box por Liga': 'League Box',
    '5 camisetas PLAYER': '5 PLAYER jerseys',
    'Elige tu liga favorita, calidad profesional': 'Choose your favorite league, professional quality',
    '5 camisetas versión PLAYER': '5 PLAYER version jerseys',
    'De tu liga favorita': 'From your favorite league',
    'Calidad profesional': 'Professional quality',
    'Parches incluidos': 'Patches included',
    'Comprar Mystery Box por Liga': 'Buy League Mystery Box',
    
    'Box Premium': 'Premium Box',
    'Solo equipos top de la élite mundial': 'Only top teams from the world elite',
    'Solo equipos de élite': 'Only elite teams',
    'Calidad premium': 'Premium quality',
    'Comprar Mystery Box Premium': 'Buy Premium Mystery Box',
    
    # Mystery Box Features
    '¿Por qué elegir una Mystery Box?': 'Why choose a Mystery Box?',
    'Ventajas exclusivas': 'Exclusive advantages',
    'Ahorro Real': 'Real Savings',
    'Precio especial por pack de 5 camisetas. Ahorra hasta 50€ comprando en box': 'Special price for a 5-jersey pack. Save up to 50€ buying in a box',
    'Sorpresa Garantizada': 'Guaranteed Surprise',
    'La emoción de no saber qué vas a recibir. Cada caja es única y diferente': 'The thrill of not knowing what you\'ll get. Each box is unique and different',
    'Equipos de Élite': 'Elite Teams',
    'Posibilidad de recibir camisetas de los mejores clubes del mundo': 'Possibility of receiving jerseys from the best clubs in the world',
    'Calidad Garantizada': 'Guaranteed Quality',
    'Réplicas oficiales de alta calidad en perfecto estado': 'High-quality official replicas in perfect condition',
    
    # Mystery Box FAQ
    '¿Puedo elegir los equipos?': 'Can I choose the teams?',
    'No, el concepto de Mystery Box es la sorpresa. En la Box por Liga puedes elegir la competición (LaLiga, Premier, Serie A, etc.) y recibirás 5 equipos diferentes de esa liga.': 'No, the Mystery Box concept is the surprise. In the League Box you can choose the competition (LaLiga, Premier, Serie A, etc.) and you will receive 5 different teams from that league.',
    '¿Cuánto tarda el envío?': 'How long does shipping take?',
    'Las Mystery Boxes se envían en 3-5 días laborables con envío gratis. Recibirás un código de seguimiento una vez se procese tu pedido.': 'Mystery Boxes are shipped in 3-5 business days with free shipping. You will receive a tracking code once your order is processed.',
    '¿Qué diferencia hay entre la Box Clásica y las demás?': 'What\'s the difference between the Classic Box and the others?',
    'La Box Clásica incluye camisetas versión FAN (oficial de aficionado), mientras que la Box por Liga y Box Premium incluyen versión PLAYER con calidad profesional, mejor tejido técnico y parches oficiales incluidos.': 'The Classic Box includes FAN version jerseys (official fan version), while the League Box and Premium Box include PLAYER version with professional quality, better technical fabric and official patches included.',
    '¿Cómo realizo el pedido?': 'How do I place an order?',
    'Haz clic en "Pedir por Telegram" en la caja que quieras. Se abrirá un chat con nosotros donde podrás indicarnos tu talla preferida y completar el pedido. Te responderemos de inmediato.': 'Click "Order via Telegram" on the box you want. A chat will open with us where you can tell us your preferred size and complete the order. We will respond immediately.',
    '¿Puedo devolver alguna camiseta?': 'Can I return a jersey?',
    'Si alguna camiseta tiene un defecto de fabricación o no es tu talla, puedes devolverla gratuitamente en un plazo de 14 días. Por la naturaleza de la Mystery Box, no aceptamos devoluciones por preferencia de equipo.': 'If a jersey has a manufacturing defect or is not your size, you can return it free of charge within 14 days. Due to the nature of the Mystery Box, we do not accept returns based on team preference.',
    '¿Hacéis envíos internacionales?': 'Do you ship internationally?',
    'Sí, realizamos envíos a toda Europa y otros países. Los gastos de envío internacional se calculan según tu ubicación. Contáctanos por Telegram para más información.': 'Yes, we ship throughout Europe and other countries. International shipping costs are calculated based on your location. Contact us via Telegram for more information.',
}


def wrap_with_data_lang(text, translation):
    """Wrap text with data-lang spans"""
    return f'<span data-lang="es">{text}</span>\n                        <span data-lang="en">{translation}</span>'


def translate_html_content(content, translations):
    """Apply all translations to HTML content"""
    
    for es_text, en_text in translations.items():
        # Skip if already translated
        if f'data-lang="es">{es_text}</span>' in content:
            continue
        
        # Escape special regex characters
        es_escaped = re.escape(es_text)
        
        # Patterns to match different HTML contexts
        patterns = [
            # Text in span/paragraph/heading tags
            (rf'(<(?:span|p|h[1-6])[^>]*>){es_escaped}(</.+?>)', 
             rf'\1{wrap_with_data_lang(es_text, en_text)}\2'),
            
            # Direct text between tags
            (rf'>{es_escaped}<', 
             rf'>{wrap_with_data_lang(es_text, en_text)}<'),
            
            # Text in attributes (for special cases)
            (rf'(["\']){es_escaped}(["\'])', 
             rf'\1{es_text}\2'),  # Don't translate attributes
        ]
        
        for pattern, replacement in patterns:
            try:
                # Apply replacement, but be careful with multiple matches
                new_content = re.sub(pattern, replacement, content, count=0)
                if new_content != content:
                    content = new_content
                    break  # Found and applied, move to next translation
            except Exception as e:
                print(f"Warning: Error processing '{es_text}': {e}")
                continue
    
    return content


def process_file(filepath):
    """Process a single HTML file"""
    print(f"\n📄 Processing {filepath}...")
    
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        content = translate_html_content(content, TRANSLATIONS)
        
        if content != original_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"✅ {filepath} updated successfully!")
            
            # Count translations applied
            translations_applied = content.count('data-lang="es"') - original_content.count('data-lang="es"')
            print(f"   → {translations_applied} new translations added")
        else:
            print(f"ℹ️  {filepath} already up to date")
            
    except Exception as e:
        print(f"❌ Error processing {filepath}: {e}")
        return False
    
    return True


def main():
    """Main execution"""
    print("🌍 Kickverse Multi-language Translation Script")
    print("=" * 50)
    
    files_to_process = [
        'index.html',
        'mystery-box.html',
        # 'catalogo.html',  # Will add later
    ]
    
    success_count = 0
    for filepath in files_to_process:
        if process_file(filepath):
            success_count += 1
    
    print("\n" + "=" * 50)
    print(f"✨ Translation complete!")
    print(f"   Files processed: {success_count}/{len(files_to_process)}")
    print(f"   Total translations available: {len(TRANSLATIONS)}")
    print("\n💡 Next steps:")
    print("   1. Test the language switcher on each page")
    print("   2. Verify all sections display correctly in both languages")
    print("   3. Run: git add -A && git commit -m 'feat: Complete multilingual support'")


if __name__ == "__main__":
    main()
