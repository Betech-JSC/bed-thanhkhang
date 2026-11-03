from PIL import Image
import os

source_path = "public/assets/images/header/hh-maker-icon.png"
if not os.path.exists(source_path):
    raise FileNotFoundError(f"Source icon not found at {source_path}")

print(f"Loading source icon: {source_path}")
src = Image.open(source_path).convert("RGBA")

# 1. public/favicon-16x16.png
icon_16 = src.resize((16, 16), Image.Resampling.LANCZOS)
icon_16.save("public/favicon-16x16.png", format="PNG")
print("Generated public/favicon-16x16.png (16x16)")

# 2. public/favicon-32x32.png
icon_32 = src.resize((32, 32), Image.Resampling.LANCZOS)
icon_32.save("public/favicon-32x32.png", format="PNG")
print("Generated public/favicon-32x32.png (32x32)")

# 3. public/apple-touch-icon.png (180x180)
icon_180 = src.resize((180, 180), Image.Resampling.LANCZOS)
icon_180.save("public/apple-touch-icon.png", format="PNG")
print("Generated public/apple-touch-icon.png (180x180)")

# 4. public/favicon.ico (16x16, 32x32, 48x48)
src.save("public/favicon.ico", format="ICO", sizes=[(16, 16), (32, 32), (48, 48)])
print("Generated public/favicon.ico with layers (16x16, 32x32, 48x48)")
