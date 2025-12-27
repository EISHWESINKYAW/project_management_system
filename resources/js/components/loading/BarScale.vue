<template>
    <div class="bars"></div>
</template>

<style>
.bars {
    width: 108px;
    /* 5 bars * 21.6px each (includes some spacing) */
    height: 70px;
    --c: linear-gradient(#828786 0 0);
    background:
        var(--c) 0% 50%,
        var(--c) 25% 50%,
        var(--c) 50% 50%,
        var(--c) 75% 50%;
    background-size:
        8px 50%,
        8px 50%,
        8px 50%,
        8px 50%;
    background-repeat: no-repeat;
    animation: bars-anim 1s infinite linear alternate;
}

@keyframes bars-anim {

    0%,
    100% {
        background-size:
            8px 50%,
            8px 50%,
            8px 50%,
            8px 50%;
    }

    20% {
        background-size:
            8px 20%,
            8px 50%,
            8px 50%,
            8px 50%;
    }

    40% {
        background-size:
            8px 100%,
            8px 20%,
            8px 50%,
            8px 50%;
    }

    60% {
        background-size:
            8px 50%,
            8px 100%,
            8px 20%,
            8px 50%;
    }

    80% {
        background-size:
            8px 50%,
            8px 50%,
            8px 100%,
            8px 20%;
    }
}
</style>
