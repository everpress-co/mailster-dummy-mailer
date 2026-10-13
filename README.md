# Mailster Dummy Mailer

Contributors: everpress, mailster, xaverb  
Tags: mailster, newsletter, email, testing, delivery  
Requires at least: 6.0  
Tested up to: 7.1  
Stable tag: 1.2.3  
License: GPLv2 or later

Simulate Mailster newsletter sending and campaign stats for development and testing—no real emails delivered.

## Description

**Mailster Dummy Mailer** is a [Mailster](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Dummy+Mailer) delivery method for WordPress newsletter development and testing. It registers as **DummyMailer** under Mailster’s delivery settings so you can run campaigns, workflows, and send tests without delivering real email to inboxes.

Use it when you need to exercise Mailster on staging or locally: practice campaign flows, tune how statistics look, and avoid accidental sends to live subscribers.

> This plugin requires the [Mailster Newsletter Plugin for WordPress](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Dummy+Mailer) **4.0** or newer. It does not send mail through SMTP or a third-party ESP—sending and engagement are simulated on your site.

### What you can simulate

- **Delivery without inbox delivery** — Outgoing Mailster sends are marked successful or failed based on your configured rates; no real messages leave the server.
- **Open, click, unsubscribe, and bounce rates** — Set target percentages so cron-driven simulation can produce campaign statistics that match your test scenario.
- **Success and campaign error rates** — Control how often a simulated send succeeds versus failing with a campaign-level or subscriber-level error.
- **Optional admin notice** — Show a clear warning on Mailster newsletter screens so you know Dummy Mailer is active.
- **Simulated client and IP data** — During simulation, random client profiles and IPs can be used for engagement events.
- **Workflows** — Compatible with Mailster 4.0+ campaign workflows (alongside finished, active, and paused campaigns).

### Who it is for

- Developers building or customizing Mailster sites
- Agencies staging newsletters before go-live
- Anyone who needs newsletter testing without a live ESP or real recipients

### Important notes

- Dummy Mailer is for **development and testing**, not production sending.
- Simulation can use significant memory; **256MB** of PHP memory (or more) is recommended.
- Switch back to Simple, SMTP, or another Mailster delivery method before real campaigns. Deactivating this plugin resets the delivery method to Simple if DummyMailer was selected.

Learn more about Mailster delivery methods in the [Mailster knowledge base](https://kb.mailster.co/choosing-your-delivery-method/).

## Installation

1. Upload the entire `mailster-dummy-mailer` folder to the `/wp-content/plugins/` directory, or install from the WordPress plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Make sure [Mailster](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Dummy+Mailer) **4.0** or newer is installed and active.
4. Go to **Settings → Newsletter → Delivery** and select the **DummyMailer** tab.
5. Adjust simulation rates if needed, then set DummyMailer as the active delivery method and save.

## Frequently Asked Questions

### Does this plugin work without Mailster?

No. Mailster Dummy Mailer is an addon for the [Mailster Newsletter Plugin](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Dummy+Mailer) and requires Mailster **4.0** or newer.

### Does Dummy Mailer send real emails?

No. It simulates delivery and (when enabled) campaign engagement. Messages are not delivered to real inboxes.

### What does Dummy Mailer do?

It adds a DummyMailer delivery method to Mailster. When selected, sends are simulated using your success and error rate settings, and optional cron simulation can generate opens, clicks, unsubscribes, and bounces toward the rates you configure.

### Will my subscribers receive newsletters?

Not while DummyMailer is the active delivery method. Use it only on development or staging sites, or when you intentionally want no real delivery.

### Can I control open and click rates?

Yes. In the DummyMailer delivery tab you can enable rate simulation and set open, click, unsubscribe, and bounce percentages, plus success and campaign error rates for the send itself.

### Why do I need more PHP memory?

Simulation walks campaigns and subscribers to apply engagement events and can be memory-intensive. At least **256MB** of PHP memory is recommended.

### Where can I get support?

For Mailster and this addon, see [mailster.co](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Dummy+Mailer) and the [WordPress.org support forum](https://wordpress.org/support/plugin/mailster-dummy-mailer/).

## Changelog

### 1.2.3

- improved: WordPress.org readme layout and changelog workflow aligned with other Mailster addons
- changed: documentation now states Mailster 4.0+ as the required Mailster version

### 1.2.2

- added: namespace
- improved: loading of plugin

### 1.2.1

- added: support for workflows from Mailster 4.0

### 1.2

- code refactor

### 1.1

- updates on rate handling

### 1.0

- initial release

## Additional Info

This plugin requires the [Mailster Newsletter Plugin for WordPress](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Dummy+Mailer) **4.0** or newer. It does not use a third-party email API; all simulation runs on your WordPress site.
