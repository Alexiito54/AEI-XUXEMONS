import express from 'express';
import cors from 'cors';
import pg from 'pg';
const app = express();
app.use(cors());
app.use(express.json());

const pool = new pg.Pool({
  host: 'BD-XUXEMONS',
  database: 'xuxuemons',
  user: 'alex',
  password: '12345',
  port: 5432
});

app.get('/api/ping', (req, res) => res.json({ ok: true }));

app.listen(3000, () => console.log('Backend 3000'));
