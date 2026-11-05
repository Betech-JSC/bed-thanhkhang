with open('resources/Frontend/js/Pages/Contact.vue', 'r', encoding='utf-8') as f:
    content = f.read()

target = """                <!-- Right Hero Logo 3D HH Maker (Figma Node 2308:8196) -->
                <img
                    src="/images/contact/hh_maker-01_2.png"
                    :alt="tt('HH Maker Logo 3D - From Ideas To Reality')"
                    class="w-[200px] sm:w-[240px] max-h-[220px] mx-auto mt-6 lg:mt-0 lg:mx-0 lg:max-h-none lg:absolute lg:w-[469px] lg:h-[889px] lg:top-[-172px] lg:left-[877px] object-contain pointer-events-none select-none"
                    loading="eager"
                />"""

replacement = """                <!-- Right Hero Logo 3D HH Maker (Figma Node 2308:8196) -->
                <img
                    src="/images/contact/hh_maker_monogram.png"
                    :alt="tt('HH Maker Logo 3D - From Ideas To Reality')"
                    class="w-[200px] sm:w-[240px] max-h-[220px] mx-auto mt-6 lg:mt-0 lg:mx-0 lg:max-h-none lg:absolute lg:w-[469px] lg:h-[889px] lg:top-[-172px] lg:left-[877px] object-contain pointer-events-none select-none opacity-20"
                    loading="eager"
                />"""

target_lf = target.replace('\r\n', '\n')
content_lf = content.replace('\r\n', '\n')

if target_lf in content_lf:
    new_content_lf = content_lf.replace(target_lf, replacement.replace('\r\n', '\n'), 1)
    with open('resources/Frontend/js/Pages/Contact.vue', 'w', encoding='utf-8', newline='\r\n') as f:
        f.write(new_content_lf)
    print('SUCCESS: Replaced image in Contact.vue')
else:
    print('ERROR: target not found')
