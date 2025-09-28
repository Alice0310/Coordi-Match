<h4>メッセージ</h4>
<div class="message-list">
    @foreach($messages as $msg)
        <div class="message-item {{ $msg->user_id === auth()->id() ? 'me' : 'other' }}">
            <strong>{{ $msg->user->nickname }}</strong>
            <p>{{ $msg->message }}</p>
            <span class="time">{{ $msg->created_at->diffForHumans() }}</span>
        </div>
    @endforeach
</div>

<form action="{{ route('trades.sendMessage', $trade->id) }}" method="POST">
    @csrf
    <textarea name="message" rows="2" placeholder="メッセージを入力..." required></textarea>
    <button type="submit" class="btn btn-primary">送信</button>
</form>