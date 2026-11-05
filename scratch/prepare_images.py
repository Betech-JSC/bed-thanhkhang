from PIL import Image

# Load original
orig = Image.open('public/images/contact/hh_maker-01_2.png')
print('Original size:', orig.size, orig.mode)

# Option A: Crop y: 0 to 2700 (bounding height = 2700)
# Option B: Clear pixels below y=2700, keeping full height 3401
# Option C: Crop tight to monogram content (with small padding)
# Content min Y: 20, max Y: 2681, min X: 445, max X: 1731

# Let's inspect what happens with Option A (crop y: 0 to 2700)
optA = orig.crop((0, 0, 2178, 2700))
optA.save('public/images/contact/hh_maker_monogram_crop2700.png')
print('Saved optA: (2178, 2700)')

# Let's inspect what happens with Option B (clear below 2700, preserve 2178x3401)
optB = orig.copy()
import numpy as np
arr = np.array(optB)
arr[2700:, :, :] = 0
optB = Image.fromarray(arr)
optB.save('public/images/contact/hh_maker_monogram_clean3401.png')
print('Saved optB: (2178, 3401 with cleared bottom)')

# Let's inspect Option C: tight crop (445, 20, 1732, 2682)
optC = orig.crop((445, 20, 1732, 2682))
optC.save('public/images/contact/hh_maker_monogram_tight.png')
print('Saved optC: tight crop', optC.size)
