@if ($templates && !$templates->isEmpty())
    @foreach ($templates as $template)
        <section class="template-{{ $template->template_type }}">
            {!! $template->content !!}
        </section>
    @endforeach
@else
    <p>No templates available.</p>
@endif
