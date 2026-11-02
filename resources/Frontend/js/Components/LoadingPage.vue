<template>
    <div class="fixed inset-0 bg-[#c53800] backdrop-blur-sm z-[9999] flex items-center justify-center">
        <div class="flex flex-col items-center justify-center space-y-10">
            <div class="xl:w-[400px] md:w-[350px] w-[300px] flex items-center justify-center">
                <JPicture src="/assets/images/header/logo.png" alt="logo" />
            </div>

            <div class="progress-bar-container">
                <div class="progress-bar-background">
                    <div class="progress-bar-fill"></div>
                    <div class="cue-stick">
                        <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.progress-bar-container {
    @apply w-[300px] h-[12px] bg-white rounded-full relative;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
}

.progress-bar-background {
    @apply w-full h-[12px] rounded-full relative overflow-visible;
    position: relative;
    background-color: transparent;
}

.progress-bar-fill {
    @apply h-[5px] bg-[#001b5e] rounded-full absolute top-[4px] left-[4px] max-w-[290px];
    width: 0%;
    animation: progress-fill 3s ease-in-out forwards;
}

.cue-stick {
    position: absolute;
    top: 25%;
    left: 0;
    transform: translateY(-50%);
    animation: cue-move 2.93s ease-in-out forwards;
    z-index: 10;
}

/* Tiến trình chạy thanh */
@keyframes progress-fill {
    from {
        width: 0%;
    }
    to {
        width: 100%;
    }
}

/* Cây bút chạy */
@keyframes cue-move {
    0% {
        left: 0;
    }
    100% {
        left: calc(100% + 20px);
        transform: translate(-100%, -50%);
    }
}
</style>
