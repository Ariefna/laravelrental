CREATE TABLE `transaksi` (
    `id` int(11) NOT NULL,
      `id_mobil` int(11) NOT NULL,
      `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
      `end_date` timestamp NOT NULL DEFAULT current_timestamp()
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ADD PRIMARY KEY (`id`);

  CREATE TABLE `mobil` (
      `id` int(11) NOT NULL,
      `nama` varchar(255) NOT NULL,
      `plat` varchar(255) NOT NULL,
      `keterangan` text NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  ALTER TABLE `transaksi` ADD PRIMARY KEY (`id`);

  ALTER TABLE `mobil` ADD PRIMARY KEY (`id`);