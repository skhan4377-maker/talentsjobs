<h2>Email Campaigns</h2>
<a href="<?= site_url('admin/campaigns/create') ?>">+ New Campaign</a>
<table border="1" cellpadding="5">
  <tr>
    <th>ID</th><th>Name</th><th>Subject</th><th>Status</th><th>Actions</th>
  </tr>
  <?php foreach ($campaigns as $c): ?>
    <tr>
      <td><?= $c->id ?></td>
      <td><?= $c->name ?></td>
      <td><?= $c->subject ?></td>
      <td><?= $c->status ?></td>
      <td>
        <a href="<?= site_url('admin/campaigns/edit/'.$c->id) ?>">Edit</a> |
        <?php if ($c->status == 'draft' || $c->status == 'scheduled'): ?>
          <a href="<?= site_url('admin/campaigns/start/'.$c->id) ?>">Start</a>
        <?php elseif ($c->status == 'active'): ?>
          <a href="<?= site_url('admin/campaigns/stop/'.$c->id) ?>">Stop</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
