#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script to add multilingual support (ES/EN) to Kickverse HTML files
"""

import re
import sys

# Translation dictionary
translations = {
    # Header & Navigation
    "Catálogo": "Catalog",
    "Mystery Box": "Mystery Box",  # Keep as is
    
    # Hero Section
    "Suscripción mensual de camisetas": "Monthly jersey subscription",
    "Recibe una camiseta de fútbol sorpresa cada mes": "Receive a surprise football jersey every month",
    "Elige tu plan y déjate sorprender. Ediciones fan, premium y retro de los mejores clubes del mundo. Cancela cuando quieras.": "Choose your plan and be surprised. Fan, premium and retro editions from the best clubs in the world. Cancel anytime.",
    "Ver planes de suscripción": "View subscription plans",
    "Suscriptores activos": "Active subscribers",
    "Valoración media": "Average rating",
    "Clientes satisfechos": "Satisfied customers",
    
    # Carousel
    "Camisetas que podrías recibir": "Jerseys you could receive",
    "Ejemplos de las equipaciones de clubes top disponibles en nuestros planes": "Examples of top club kits available in our plans",
    
    # Plans Section
    "Elige tu suscripción": "Choose your subscription",
    "Todos los planes incluyen envío gratuito a España": "All plans include free shipping to Spain",
    "Esencial": "Essential",
    "Plan Fan": "Fan Plan",
    "por mes": "per month",
    "Ideal para aficionados y coleccionistas casuales": "Perfect for fans and casual collectors",
    "Camiseta FAN aleatoria": "Random FAN jersey",
    "Equipaciones 2024/25": "2024/25 kits",
    "Envío incluido": "Shipping included",
    "Cancela cuando quieras": "Cancel anytime",
    "Suscribirme al Plan Fan": "Subscribe to Fan Plan",
    
    "El más elegido": "Most popular",
    "Plan Premium Random": "Premium Random Plan",
    "Camiseta tipo player de equipos medios": "Player-type jersey from mid-tier teams",
    "Camiseta Player aleatoria": "Random Player jersey",
    "Equipos de nivel medio": "Mid-tier teams",
    "Material de calidad": "Quality material",
    "Suscribirme al Premium Random": "Subscribe to Premium Random",
    
    "Clubes TOP": "TOP Clubs",
    "Plan Premium Equipo TOP": "Premium TOP Team Plan",
    "Camiseta tipo player de clubes top": "Player-type jersey from top clubs",
    "Camiseta Player de clubes TOP": "Player jersey from TOP clubs",
    "Madrid, Barça, City, PSG...": "Madrid, Barça, City, PSG...",
    "Versión profesional": "Professional version",
    "Suscribirme al Premium TOP": "Subscribe to Premium TOP",
    
    "Exclusivo": "Exclusive",
    "Plan Retro TOP": "Retro TOP Plan",
    "Camiseta retro mítica de selecciones o clubes legendarios": "Legendary retro jersey from national teams or legendary clubs",
    "Camiseta RETRO legendaria": "Legendary RETRO jersey",
    "Selecciones y clubes míticos": "National teams and legendary clubs",
    "Ediciones icónicas": "Iconic editions",
    "Suscribirme al Retro TOP": "Subscribe to Retro TOP",
    
    # Features Section
    "¿Cómo funciona?": "How does it work?",
    "Sencillo, emocionante y sin compromisos": "Simple, exciting, and no commitments",
    "1. Elige tu plan": "1. Choose your plan",
    "Selecciona el plan que mejor se adapte a ti y suscríbete en segundos con Stripe": "Select the plan that best suits you and subscribe in seconds with Stripe",
    "2. Recibe tu sorpresa": "2. Receive your surprise",
    "Cada mes recibirás una camiseta aleatoria de clubes top o ediciones especiales": "Every month you'll receive a random jersey from top clubs or special editions",
    "3. Disfruta y colecciona": "3. Enjoy and collect",
    "Amplía tu colección sin esfuerzo. Cancela o cambia de plan cuando quieras": "Expand your collection effortlessly. Cancel or change plans anytime",
    "Envío gratuito": "Free shipping",
    "Todos los planes incluyen envío gratuito a toda España. Sin costes ocultos": "All plans include free shipping throughout Spain. No hidden costs",
    
    # FAQ Section
    "Preguntas frecuentes": "Frequently asked questions",
    "Todo lo que necesitas saber sobre tu suscripción": "Everything you need to know about your subscription",
    "¿Cómo funciona la suscripción?": "How does the subscription work?",
    "Al suscribirte, recibirás automáticamente una camiseta sorpresa cada mes según el plan elegido. El pago se realiza mensualmente de forma automática y puedes cancelar en cualquier momento.": "When you subscribe, you will automatically receive a surprise jersey every month according to your chosen plan. Payment is made monthly automatically and you can cancel at any time.",
    "¿Puedo elegir la camiseta que recibo?": "Can I choose the jersey I receive?",
    "No, la emoción está en la sorpresa. Seleccionamos cuidadosamente camisetas de alta calidad de clubes top, selecciones y ediciones especiales. Puedes indicar tu talla y preferencias generales contactándonos por Telegram.": "No, the excitement is in the surprise. We carefully select high-quality jerseys from top clubs, national teams and special editions. You can indicate your size and general preferences by contacting us via Telegram.",
    "¿Cómo cancelo mi suscripción?": "How do I cancel my subscription?",
    "Puedes cancelar en cualquier momento desde tu panel de Stripe o contactándonos por Telegram (@esKickverse). No hay penalizaciones ni periodos mínimos.": "You can cancel at any time from your Stripe panel or by contacting us via Telegram (@esKickverse). There are no penalties or minimum periods.",
    "¿Cuándo recibiré mi primera camiseta?": "When will I receive my first jersey?",
    "Tu primera camiseta se enviará dentro de los 5-7 días hábiles tras confirmar tu suscripción. Los envíos posteriores se realizarán mensualmente en la misma fecha.": "Your first jersey will be sent within 5-7 business days after confirming your subscription. Subsequent shipments will be made monthly on the same date.",
    "¿Qué pasa si la camiseta no me queda bien?": "What if the jersey doesn't fit me well?",
    "Antes de tu primer envío, confirmaremos tu talla por Telegram. Si hay algún problema, contáctanos y buscaremos una solución.": "Before your first shipment, we will confirm your size via Telegram. If there's any problem, contact us and we'll find a solution.",
    "¿Hacéis envíos fuera de España?": "Do you ship outside Spain?",
    "Sí, realizamos envíos a toda Europa y otros países. Los gastos de envío internacional se calculan automáticamente según tu ubicación. Contáctanos por Telegram para más información sobre envíos a tu país.": "Yes, we ship throughout Europe and other countries. International shipping costs are automatically calculated based on your location. Contact us via Telegram for more information about shipping to your country.",
    
    # Footer
    "Tu suscripción mensual de camisetas de fútbol. Recibe sorpresas cada mes y amplía tu colección sin esfuerzo.": "Your monthly football jersey subscription. Receive surprises every month and expand your collection effortlessly.",
    "Suscripciones": "Subscriptions",
    "Legal": "Legal",
    "Términos y Condiciones": "Terms and Conditions",
    "Política de Privacidad": "Privacy Policy",
    "Política de Envíos": "Shipping Policy",
    "Devoluciones": "Returns",
    "Contacto": "Contact",
    "Telegram": "Telegram",
    "Email": "Email",
    "Todos los derechos reservados": "All rights reserved",
    
    # Floating CTA
    "¡Suscríbete ahora!": "Subscribe now!",
}


def wrap_text_with_lang(text):
    """Wrap text with data-lang attributes for both ES and EN"""
    es_text = text.strip()
    en_text = translations.get(es_text, es_text)
    
    if es_text == en_text:
        # No translation available, return original
        return es_text
    
    return f'<span data-lang="es">{es_text}</span>\n                        <span data-lang="en">{en_text}</span>'


def main():
    if len(sys.argv) > 1:
        input_file = sys.argv[1]
    else:
        input_file = "index.html"
    
    output_file = input_file.replace(".html", "_multilang.html")
    
    print(f"Processing {input_file}...")
    print(f"Output will be saved to {output_file}")
    print(f"Total translations: {len(translations)}")
    
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Apply translations
    for es_text, en_text in translations.items():
        # Skip if already translated
        if 'data-lang="es"' in content and es_text in content:
            continue
        
        # Different patterns for different HTML contexts
        patterns = [
            # For text in tags like <h1>, <h2>, <h3>, <p>, <span>
            (rf'(<[^>]+>){re.escape(es_text)}(</.+?>)', rf'\1<span data-lang="es">{es_text}</span>\n                        <span data-lang="en">{en_text}</span>\2'),
            # For text directly in tags
            (rf'>{re.escape(es_text)}<', rf'><span data-lang="es">{es_text}</span>\n                        <span data-lang="en">{en_text}</span><'),
        ]
        
        for pattern, replacement in patterns:
            content = re.sub(pattern, replacement, content)
    
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✓ File processed successfully!")
    print(f"✓ Saved to: {output_file}")


if __name__ == "__main__":
    main()
