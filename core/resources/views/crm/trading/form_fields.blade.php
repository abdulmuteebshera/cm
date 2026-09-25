<label>Name<input name="name" required></label>
<label>Asset class<input name="asset_class" placeholder="Equities / FX / Commodities"></label>
<label>Venue<input name="venue"></label>
<label>Symbol<input name="symbol"></label>
<label>Currency<input name="currency" value="USD"></label>
<label>Allocated<input type="number" step="0.01" name="allocated_amount" required></label>
<label>Market value<input type="number" step="0.01" name="market_value"></label>
<label>P&L<input type="number" step="0.01" name="pnl"></label>
<label>P&L %<input type="number" step="0.01" name="pnl_percent"></label>
<label>Status
    <select name="status">
        <option value="open">open</option>
        <option value="pending">pending</option>
        <option value="closed">closed</option>
    </select>
</label>
<label>Opened on<input type="date" name="opened_on"></label>
<label>Notes<textarea name="notes" rows="2"></textarea></label>
