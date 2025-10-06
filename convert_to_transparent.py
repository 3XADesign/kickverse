#!/usr/bin/env python3
"""
Script para convertir imágenes JPG con fondo blanco a PNG con transparencia
"""

from PIL import Image
import os
from pathlib import Path

def remove_white_background(input_path, output_path, threshold=240):
    """
    Remueve el fondo blanco de una imagen y la guarda como PNG transparente
    
    Args:
        input_path: Ruta de la imagen de entrada (JPG)
        output_path: Ruta de la imagen de salida (PNG)
        threshold: Valor umbral para considerar un pixel como blanco (0-255)
    """
    try:
        # Abrir imagen
        img = Image.open(input_path)
        
        # Convertir a RGBA si no lo está
        img = img.convert("RGBA")
        
        # Obtener datos de píxeles
        datas = img.getdata()
        
        # Crear nueva lista de píxeles
        new_data = []
        
        for item in datas:
            # Si el pixel es blanco (o casi blanco), hacerlo transparente
            if item[0] > threshold and item[1] > threshold and item[2] > threshold:
                # Hacer transparente (alpha = 0)
                new_data.append((255, 255, 255, 0))
            else:
                # Mantener pixel original
                new_data.append(item)
        
        # Actualizar datos de imagen
        img.putdata(new_data)
        
        # Guardar como PNG
        img.save(output_path, "PNG")
        print(f"✓ Convertido: {os.path.basename(input_path)} -> {os.path.basename(output_path)}")
        return True
        
    except Exception as e:
        print(f"✗ Error procesando {input_path}: {str(e)}")
        return False

def process_directory(input_dir, output_dir=None, threshold=240):
    """
    Procesa todas las imágenes JPG en un directorio
    
    Args:
        input_dir: Directorio con las imágenes JPG
        output_dir: Directorio de salida (si es None, sobrescribe las originales)
        threshold: Valor umbral para considerar un pixel como blanco
    """
    input_path = Path(input_dir)
    
    if output_dir:
        output_path = Path(output_dir)
        output_path.mkdir(parents=True, exist_ok=True)
    else:
        output_path = input_path
    
    # Buscar todas las imágenes JPG
    jpg_files = list(input_path.glob("*.jpg")) + list(input_path.glob("*.jpeg"))
    
    if not jpg_files:
        print(f"No se encontraron archivos JPG en {input_dir}")
        return
    
    print(f"\nProcesando {len(jpg_files)} imágenes en {input_dir}...")
    print(f"Umbral de blanco: {threshold}/255\n")
    
    success_count = 0
    
    for jpg_file in jpg_files:
        # Cambiar extensión a .png
        png_filename = jpg_file.stem + ".png"
        png_file = output_path / png_filename
        
        if remove_white_background(str(jpg_file), str(png_file), threshold):
            success_count += 1
    
    print(f"\n✓ Procesadas exitosamente: {success_count}/{len(jpg_files)} imágenes")
    
    if output_dir is None:
        print(f"\n⚠️  Las imágenes PNG se guardaron en el mismo directorio.")
        print(f"   Recuerda actualizar las rutas en el código HTML/JS para usar .png en lugar de .jpg")

if __name__ == "__main__":
    import sys
    
    # Directorio de camisetas
    camisetas_dir = "img/camisetas"
    
    if not os.path.exists(camisetas_dir):
        print(f"✗ Error: No se encontró el directorio {camisetas_dir}")
        sys.exit(1)
    
    print("=" * 60)
    print("KICKVERSE - Conversor de imágenes a transparente")
    print("=" * 60)
    
    # Procesar imágenes (threshold ajustable: 240 es un buen balance)
    # Valores más bajos (ej: 200) eliminan más fondo pero pueden afectar detalles
    # Valores más altos (ej: 250) son más conservadores
    
    process_directory(camisetas_dir, threshold=240)
    
    print("\n" + "=" * 60)
    print("SIGUIENTE PASO:")
    print("Actualiza catalog.js para cambiar las extensiones de .jpg a .png")
    print("=" * 60)
