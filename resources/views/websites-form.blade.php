

@include('maelstrom::inputs.text', [
    'name' => 'url',
    'label' => 'Website URL',
    'html_type' => 'url',
    'prefix' => '🔗',
])

<div class="flex flex-wrap justify-between">

    @include('maelstrom::inputs.switch', [
        'name' => 'uptime_enabled',
        'label' => 'Enable Up-Time Monitoring?',
        'hide_off' => ['uptime_keyword'],
    ])

    @include('maelstrom::inputs.switch', [
        'name' => 'ssl_enabled',
        'label' => 'Enable SSL Monitoring?'
    ])

    @include('maelstrom::inputs.switch', [
        'name' => 'robots_enabled',
        'label' => 'Enable Robots.txt Monitoring?'
    ])

    @include('maelstrom::inputs.switch', [
        'name' => 'dns_enabled',
        'label' => 'Enable DNS Monitoring?'
    ])

    @include('maelstrom::inputs.switch', [
        'name' => 'cron_enabled',
        'label' => 'Enable Cron Monitoring?',
        'hide_off' => ['cron_key', 'cron_info'],
    ])
</div>

@include('maelstrom::inputs.text', [
    'name' => 'uptime_keyword',
    'label' => 'Uptime Keyword',
    'help' => 'This word *must* exist on the web page to confirm the site is online.',
    'prefix' => '🔑',
    'required' => true,
])

@php($cronKey = data_get($entry, 'cron_key', Str::random(32)))

@include('maelstrom::inputs.random', [
    'name' => 'cron_key',
    'label' => 'Cron API Key',
    'prefix' => '🔒',
    'required' => true,
    'default' => $cronKey,
    'length' => 32,
    'charset' => 'qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM',
])

<div id="cron_info_field" class="cloak">
    <p>
        When your scheduled task starts, you should ping:
        <pre><code>{{ route('ping.start', ['website' => $entry, 'key' => $cronKey]) }}</code></pre>
    </p>

    <p>
        When your scheduled task finishes, you should ping:
        <pre><code>{{ route('ping.stop', ['website' => $entry, 'key' => $cronKey]) }}</code></pre>
    </p>

    <p>
        You can append extra query strings to the URL to help identify your events. e.g.
        <pre><code>{{ route('ping.stop', ['website' => $entry, 'task' => 'Optimise-Images']) }}</code></pre>
    </p>
</div>
