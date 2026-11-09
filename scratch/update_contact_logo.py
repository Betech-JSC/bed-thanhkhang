with open('resources/Frontend/js/Pages/Contact.vue', 'r', encoding='utf-8') as f:
    text = f.read()

old_sec = 'class="relative w-full overflow-hidden pt-12 md:pt-16 pb-20 md:pb-28 lg:py-0 lg:h-[545px]"'
new_sec = 'class="relative w-full overflow-hidden lg:overflow-visible pt-12 md:pt-16 pb-20 md:pb-28 lg:py-0 lg:h-[545px]"'
assert old_sec in text, 'old_sec not found'
text = text.replace(old_sec, new_sec, 1)

old_div = '''                <!-- Right Hero Logo 3D HH Maker (Figma Node 2308:8196: cropped 469x483 visible window at top 0, left 877px) -->
                <div
                    class="w-[200px] sm:w-[240px] max-h-[220px] mx-auto mt-6 lg:mt-0 lg:mx-0 lg:max-h-none lg:absolute lg:w-[469px] lg:h-[483px] lg:top-0 lg:left-[877px] lg:overflow-hidden pointer-events-none select-none opacity-20"
                >
                    <img
                        src="/images/contact/hh_maker_monogram.png"
                        :alt="tt('HH Maker Logo 3D - From Ideas To Reality')"
                        class="w-full h-full object-contain lg:w-[794px] lg:h-[1136px] lg:max-w-none lg:-ml-[162px] lg:-mt-[179px] lg:object-fill"
                        loading="eager"
                    />
                </div>'''

new_div = '''                <!-- Right Hero Logo 3D HH Maker (Figma Node 2308:8196: 469x889 at left 1021px, top -172px) -->
                <div
                    class="w-[200px] sm:w-[240px] max-h-[220px] mx-auto mt-6 lg:mt-0 lg:mx-0 lg:max-h-none lg:absolute lg:w-[469px] lg:h-[889px] lg:-top-[172px] lg:left-[909px] pointer-events-none select-none opacity-20"
                >
                    <img
                        src="/images/contact/hh_maker_monogram.png"
                        :alt="tt('HH Maker Logo 3D - From Ideas To Reality')"
                        class="w-full h-full object-contain pointer-events-none select-none"
                        loading="eager"
                    />
                </div>'''

assert old_div in text, 'old_div not found'
text = text.replace(old_div, new_div, 1)

with open('resources/Frontend/js/Pages/Contact.vue', 'w', encoding='utf-8', newline='\n') as f:
    f.write(text)

print('Contact.vue successfully updated with exact LF line endings!')
