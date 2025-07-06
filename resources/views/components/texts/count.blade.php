{{--
    //styleName: Titulo;
    font-family: Poppins;
    font-size: 18px;
    font-weight: 600;
    line-height: 27px;
    text-align: left;
--}}
<h2 {{ $attributes->merge(['class' => 'text-lg font-bold text-tx-black flex flex-row justify-start items-center space-x-2']) }}>
    {{ $slot }}
</h2>
