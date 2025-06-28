<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary-auth']) }}>
    {{ $slot }}
</button>