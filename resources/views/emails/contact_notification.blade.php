<p>You have received a new contact message from the website:</p>

<ul>
    <li><strong>Name:</strong> {{ $contact->name ?? '-' }}</li>
    <li><strong>Email:</strong> {{ $contact->email ?? '-' }}</li>
    <li><strong>Phone:</strong> {{ $contact->phone ?? '-' }}</li>
    <li><strong>Subject:</strong> {{ $contact->subject ?? '-' }}</li>
    <li><strong>Message:</strong><br>{!! nl2br(e($contact->message)) !!}</li>
    <li><strong>Time:</strong> {{ $contact->created_at ?? now() }}</li>
</ul>

<p>Please reply as needed.</p>
