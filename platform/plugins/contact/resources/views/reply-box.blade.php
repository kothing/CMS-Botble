@if ($contact)
    <div id="reply-wrapper">
        @if (count($contact->replies) > 0)
            @foreach($contact->replies as $reply)
                <p>{{ trans('plugins/contact::contact.tables.time') }}: <i>{{ $reply->created_at }}</i></p>
                <p>{{ trans('plugins/contact::contact.tables.content') }}:</p>
                <pre class="message-content">{!! BaseHelper::clean($reply->message) !!}</pre>
            @endforeach
        @else
            <p>{{ trans('plugins/contact::contact.no_reply') }}</p>
        @endif
    </div>

    <p><button class="btn btn-info answer-trigger-button">{{ trans('plugins/contact::contact.reply') }}</button></p>

    <div class="answer-wrapper">
        <div class="form-group mb-3">
            {!! Form::editor('message', null, ['without-buttons' => true, 'class' => 'form-control']) !!}
        </div>

        <div class="form-group mb-3">
            <input type="hidden" value="{{ $contact->id }}" id="input_contact_id">
            <button class="btn btn-success answer-send-button"><i class="fas fa-reply"></i> {{ trans('plugins/contact::contact.send') }}</button>
        </div>
    </div>
@endif
