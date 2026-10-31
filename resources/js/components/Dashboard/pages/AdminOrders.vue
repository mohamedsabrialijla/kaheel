<template>
    <div class="ka-orders">
        <template v-if="!orderId">
            <div class="ka-sup-head">
                <div>
                    <p class="ka-sup-kicker">ORDER OPERATIONS</p>
                    <h1>{{ t.title }}</h1>
                    <p class="ka-sup-lead">{{ t.lead }}</p>
                </div>
            </div>
            <div class="ka-ord-filters">
                <label class="ka-field ka-ord-search">
                    <span class="ka-sr">{{ t.search }}</span>
                    <input v-model="query" :placeholder="t.search">
                </label>
                <label class="ka-field">
                    <span class="ka-sr">{{ t.stage }}</span>
                    <select v-model="stage">
                        <option value="all">{{ t.allStages }}</option>
                        <option v-for="item in stages" :key="item.code" :value="item.code">{{ label(item) }}</option>
                    </select>
                </label>
            </div>
            <div class="ka-ord-mobile-list">
                <p v-if="loading" class="ka-empty">…</p>
                <p v-else-if="!filtered.length" class="ka-empty">{{ t.empty }}</p>
                <template v-else>
                    <button v-for="row in filtered" :key="'m' + row.id" type="button" class="ka-ord-mobile-card" @click="open(row.id)">
                        <div class="ka-ord-mobile-top">
                            <div>
                                <p class="ka-ord-strong">{{ row.number }} <span v-if="row.unread" class="ka-unread">{{ row.unread }}</span></p>
                                <p class="ka-sup-muted">{{ date(row.created_at) }}</p>
                            </div>
                            <span class="ka-ord-badge" :class="'is-' + row.status">{{ locale === 'en' ? row.status_en : row.status_ar }}</span>
                        </div>
                        <p class="ka-ord-strong">{{ row.customer_name }}</p>
                        <p class="ka-sup-muted">{{ row.unit_no }} · {{ row.compound_name }}</p>
                        <p class="ka-ord-strong">{{ money(row.amount) }} <span class="ka-sup-muted">SAR</span></p>
                        <progress-track :percent="row.percent" :step="row.step" :label="t.progress"></progress-track>
                    </button>
                </template>
            </div>
            <div class="ka-table-wrap ka-ord-table">
                <table class="ka-table wide">
                    <thead>
                        <tr>
                            <th>{{ t.order }}</th>
                            <th>{{ t.customer }}</th>
                            <th>{{ t.amount }}</th>
                            <th>{{ t.stage }}</th>
                            <th>{{ t.progress }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading"><td class="ka-empty" colspan="6">…</td></tr>
                        <tr v-else-if="!filtered.length"><td class="ka-empty" colspan="6">{{ t.empty }}</td></tr>
                        <tr v-for="row in filtered" :key="row.id">
                            <td>
                                <p class="ka-ord-strong">{{ row.number }} <span v-if="row.unread" class="ka-unread">{{ row.unread }}</span></p>
                                <p class="ka-sup-muted">{{ date(row.created_at) }}</p>
                            </td>
                            <td>
                                <p class="ka-ord-strong">{{ row.customer_name }}</p>
                                <p class="ka-sup-muted">{{ row.unit_no }} · {{ row.compound_name }}</p>
                            </td>
                            <td class="ka-ord-strong">{{ money(row.amount) }} <span class="ka-sup-muted">SAR</span></td>
                            <td><span class="ka-ord-badge" :class="'is-' + row.status">{{ locale === 'en' ? row.status_en : row.status_ar }}</span></td>
                            <td><progress-track :percent="row.percent" :step="row.step" :label="t.progress"></progress-track></td>
                            <td><button type="button" class="ka-btn ka-btn-outline" @click="open(row.id)">{{ t.details }}</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template v-else-if="order">
            <div class="ka-ord-hero">
                <div>
                    <button type="button" class="ka-btn ka-btn-ghost" @click="back">{{ t.all }}</button>
                    <h1>{{ order.number }}</h1>
                    <p class="ka-sup-lead">{{ order.customer_name }} · {{ order.unit_no }} · {{ order.compound_name }}</p>
                </div>
                <span class="ka-ord-badge" :class="'is-' + order.status">{{ locale === 'en' ? order.status_en : order.status_ar }}</span>
            </div>
            <section class="ka-ord-card">
                <progress-track :percent="order.percent" :step="order.step" :label="t.progress" wide></progress-track>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.info }}</h2>
                <div class="ka-ord-grid">
                    <div><p>{{ t.customerName }}</p><strong>{{ order.customer_name }}</strong></div>
                    <div><p>{{ t.username }}</p><strong>{{ order.username }}</strong></div>
                    <div><p>{{ t.project }}</p><strong>{{ order.compound_name }}</strong></div>
                    <div><p>{{ t.unit }}</p><strong>{{ order.unit_no }}<template v-if="order.area"> · {{ order.area }} m²</template></strong></div>
                    <div><p>{{ t.bedrooms }}</p><strong>{{ order.bedrooms || '—' }}</strong></div>
                    <div><p>{{ t.package }}</p><strong>{{ locale === 'en' ? order.package_en : order.package_ar }}</strong></div>
                    <div><p>{{ t.tier }}</p><strong>{{ locale === 'en' ? order.tier_en : order.tier_ar }}</strong></div>
                    <div><p>{{ t.created }}</p><strong>{{ date(order.created_at) }}</strong></div>
                    <div><p>{{ t.designStatus }}</p><strong>{{ designLabel }}</strong></div>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.financials }}</h2>
                <div class="ka-ord-grid">
                    <div><p>{{ t.excl }}</p><strong>{{ money(order.price_excl) }} SAR</strong></div>
                    <div><p>{{ t.vat }}</p><strong>{{ money(order.vat) }} SAR</strong></div>
                    <div><p>{{ t.packageTotal }}</p><strong>{{ money(order.price_incl) }} SAR</strong></div>
                    <div><p>{{ t.addons }}</p><strong>{{ money(billAddons) }} SAR</strong></div>
                    <div><p>{{ t.finalTotal }}</p><strong>{{ money(billFinal) }} SAR</strong></div>
                    <div><p>{{ t.deposit }}</p><strong>{{ money(order.deposit_amount) }} SAR · {{ order.deposit_paid ? t.paidState : t.unpaid }}</strong></div>
                    <div><p>{{ t.paid }}</p><strong>{{ money(order.paid) }} SAR</strong></div>
                    <div><p>{{ t.remaining }}</p><strong>{{ money(billRemaining) }} SAR</strong></div>
                </div>
                <h3 class="ka-ord-subhead">{{ t.payments }}</h3>
                <p v-if="!paymentEntries.length" class="ka-sup-muted">{{ t.noPayments }}</p>
                <div v-else class="ka-ord-pay-list">
                    <article v-for="row in paymentEntries" :key="row.id" class="ka-ord-pay-row" :class="{ 'is-rejected': row.state === 'rejected' }">
                        <div class="ka-ord-pay-main">
                            <strong>{{ row.label }} — {{ row.ref }}</strong>
                            <span class="ka-sup-muted">{{ row.method }} · <span class="ka-pay-status" :class="{ 'is-rejected': row.state === 'rejected' }">{{ row.status }}</span> · {{ date(row.at) }}</span>
                        </div>
                        <div class="ka-ord-pay-side">
                            <strong>{{ money(row.amount) }} SAR</strong>
                            <a v-if="row.url" class="ka-ord-file" :href="row.url" target="_blank" rel="noopener noreferrer">{{ t.viewReceipt }}</a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.paymentSection }}</h2>
                <p v-if="!order.payment_method" class="ka-sup-muted">{{ t.noPayments }}</p>
                <template v-else>
                    <div class="ka-ord-grid">
                        <div><p>{{ t.method }}</p><strong>{{ methodLabel }}</strong></div>
                        <div><p>{{ t.amount }}</p><strong>{{ money(order.deposit_amount) }} SAR</strong></div>
                        <div><p>{{ t.payState }}</p><strong>{{ payStateLabel }}</strong></div>
                    </div>
                    <p v-if="order.payment_status === 'approved' && !needsFinalReview" class="ka-ord-ok">{{ t.paymentOk }}</p>
                    <p v-if="order.payment_note" class="ka-ord-error">{{ order.payment_note }}</p>
                    <div v-if="order.payment_status === 'pending' && can('orders.edit')" class="ka-ord-pay-actions">
                        <label class="ka-field"><span>{{ t.rejectNote }}</span><textarea v-model="paymentNote" rows="3" :placeholder="t.rejectPlaceholder"></textarea></label>
                        <div class="ka-ord-actions">
                            <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="reviewPayment('approved')">{{ t.approvePay }}</button>
                            <button type="button" class="ka-btn ka-btn-danger" :disabled="saving || !paymentNote.trim()" @click="reviewPayment('rejected')">{{ t.rejectPay }}</button>
                        </div>
                    </div>
                </template>
                <div v-if="needsFinalReview && can('orders.edit')" class="ka-ord-pay-actions ka-ord-final-only">
                    <h3>{{ t.finalReviewTitle }}</h3>
                    <p class="ka-sup-muted">{{ t.finalPending }}</p>
                    <label class="ka-field"><span>{{ t.rejectNote }}</span><textarea v-model="paymentNote" rows="3" :placeholder="t.rejectPlaceholder"></textarea></label>
                    <div class="ka-ord-actions">
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="confirmFinal('approved')">{{ t.confirmFinal }}</button>
                        <button type="button" class="ka-btn ka-btn-danger" :disabled="saving || !paymentNote.trim()" @click="confirmFinal('rejected')">{{ t.rejectPay }}</button>
                    </div>
                </div>
                <p v-else-if="order.final_payment_status === 'approved'" class="ka-ord-ok">{{ t.finalOk }}</p>
                <template v-else-if="order.final_payment_status === 'rejected'">
                    <p v-if="order.final_payment_note" class="ka-ord-error">{{ order.final_payment_note }}</p>
                    <p class="ka-sup-muted">{{ t.finalRejectedWait }}</p>
                </template>
                <p v-else-if="order.status === 'final_payment'" class="ka-sup-muted">{{ t.finalWaitCustomer }}</p>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.customerPlan }}</h2>
                <p v-if="!plans.length" class="ka-sup-muted">{{ t.noPlan }}</p>
                <div v-else class="ka-file-grid">
                    <a v-for="file in plans" :key="'plan-' + file.id" class="ka-file-tile" :href="file.url" target="_blank" rel="noopener noreferrer">
                        <span class="ka-file-thumb" :class="{ 'is-pdf': isPdf(file) }" :style="thumbStyle(file)">
                            <span v-if="isPdf(file)" class="ka-file-pdf" aria-hidden="true">PDF</span>
                        </span>
                        <span class="ka-file-name">{{ file.name }}</span>
                    </a>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.workspace }}</h2>
                <p class="ka-ord-hint">{{ hint }}</p>
                <p v-if="!canOperate && order.payment_method" class="ka-ord-hint">{{ t.confirmFirst }}</p>

                <div class="ka-ord-stack">
                <div class="ka-ord-panel">
                <h3>{{ t.designFiles }}</h3>
                <p v-if="!designFiles.length" class="ka-sup-muted">{{ t.none }}</p>
                <div v-else class="ka-file-grid">
                    <a v-for="file in designFiles" :key="file.id" class="ka-file-tile" :href="file.url" target="_blank" rel="noopener noreferrer">
                        <span class="ka-file-thumb" :class="{ 'is-pdf': isPdf(file) }" :style="thumbStyle(file)">
                            <span v-if="isPdf(file)" class="ka-file-pdf" aria-hidden="true">PDF</span>
                        </span>
                        <span class="ka-file-name">{{ file.name }}</span>
                        <span class="ka-file-time">{{ date(file.created_at) }}</span>
                    </a>
                </div>
                <h3 class="ka-ord-sub">{{ t.handoverFiles }}</h3>
                <p v-if="needsHandover" class="ka-ord-error">{{ t.handoverRequired }}</p>
                <p v-else-if="!handovers.length" class="ka-sup-muted">{{ t.none }}</p>
                <div v-if="handovers.length" class="ka-file-grid">
                    <a v-for="file in handovers" :key="'handover-' + file.id" class="ka-file-tile" :href="file.url" target="_blank" rel="noopener noreferrer">
                        <span class="ka-file-thumb" :class="{ 'is-pdf': isPdf(file) }" :style="thumbStyle(file)">
                            <span v-if="isPdf(file)" class="ka-file-pdf" aria-hidden="true">PDF</span>
                        </span>
                        <span class="ka-file-name">{{ file.name }}</span>
                    </a>
                </div>
                <h3 class="ka-ord-sub">{{ t.otherDocs }}</h3>
                <button type="button" class="ka-btn ka-btn-outline" @click="contractOpen = !contractOpen">{{ t.contract }}</button>
                <pre v-if="contractOpen" class="ka-ord-contract">{{ order.contract_body }}</pre>
                <div v-if="otherFiles.length" class="ka-file-grid">
                    <a v-for="file in otherFiles" :key="'other-' + file.id" class="ka-file-tile" :href="file.url" target="_blank" rel="noopener noreferrer">
                        <span class="ka-file-thumb" :class="{ 'is-pdf': isPdf(file) }" :style="thumbStyle(file)">
                            <span v-if="isPdf(file)" class="ka-file-pdf" aria-hidden="true">PDF</span>
                        </span>
                        <span class="ka-file-name">{{ file.name }}</span>
                    </a>
                </div>

                <div v-if="canUpload && canOperate && can('orders.edit')" class="ka-ord-uploads">
                    <div class="ka-field">
                        <span>{{ t.uploadDesign }}</span>
                        <div class="ka-drop" :class="{ 'is-over': designOver }" @dragenter.prevent="designOver = true" @dragover.prevent="designOver = true" @dragleave.prevent="designOver = false" @drop.prevent="onDrop($event, 'design')">
                            <button type="button" class="ka-drop-zone" @click="$refs.designPick.click()">
                                <strong>{{ t.dropFiles }}</strong>
                                <span>{{ t.designLimit }}</span>
                            </button>
                            <div class="ka-drop-list">
                                <div v-for="(item, index) in designQueue" :key="item.id" class="ka-drop-item">
                                    <span class="ka-drop-thumb" :class="{ 'is-pdf': item.pdf }" :style="item.preview ? { backgroundImage: 'url(' + item.preview + ')' } : null">{{ item.pdf ? 'PDF' : '' }}</span>
                                    <span class="ka-drop-meta"><strong>{{ item.file.name }}</strong><em>{{ item.size }}</em></span>
                                    <button type="button" class="ka-drop-x" @click="removeQueued('design', index)">×</button>
                                </div>
                            </div>
                            <input ref="designPick" class="ka-drop-input" type="file" accept="image/*,.jpg,.jpeg,.jfif,.png,.webp,.gif,.bmp,.tif,.tiff,.heic,.heif,.avif,.pdf,application/pdf" multiple @change="queueFiles($event, 'design')">
                            <button v-if="designQueue.length" type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="uploadQueued('design')">{{ t.uploadNow }}</button>
                        </div>
                    </div>
                    <div class="ka-field" :class="{ 'is-required': needsHandover }">
                        <span>{{ t.uploadHandover }}</span>
                        <div class="ka-drop" :class="{ 'is-over': handoverOver }" @dragenter.prevent="handoverOver = true" @dragover.prevent="handoverOver = true" @dragleave.prevent="handoverOver = false" @drop.prevent="onDrop($event, 'handover')">
                            <button type="button" class="ka-drop-zone" @click="$refs.handoverPick.click()">
                                <strong>{{ t.dropFiles }}</strong>
                                <span>{{ t.handoverHint }}</span>
                            </button>
                            <div class="ka-drop-list">
                                <div v-for="(item, index) in handoverQueue" :key="item.id" class="ka-drop-item">
                                    <span class="ka-drop-thumb" :class="{ 'is-pdf': item.pdf }" :style="item.preview ? { backgroundImage: 'url(' + item.preview + ')' } : null">{{ item.pdf ? 'PDF' : '' }}</span>
                                    <span class="ka-drop-meta"><strong>{{ item.file.name }}</strong><em>{{ item.size }}</em></span>
                                    <button type="button" class="ka-drop-x" @click="removeQueued('handover', index)">×</button>
                                </div>
                            </div>
                            <input ref="handoverPick" class="ka-drop-input" type="file" accept="image/*,.jpg,.jpeg,.jfif,.png,.webp,.gif,.bmp,.tif,.tiff,.heic,.heif,.avif,.pdf,application/pdf" multiple @change="queueFiles($event, 'handover')">
                            <button v-if="handoverQueue.length" type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="uploadQueued('handover')">{{ t.uploadNow }}</button>
                        </div>
                    </div>
                </div>
                </div>

                <div class="ka-ord-panel">
                <h3>{{ t.components }}</h3>
                <template v-if="order.status !== 'design_preparation'">
                <p v-if="!order.components.length" class="ka-sup-muted">{{ t.none }}</p>
                <div v-for="item in order.components" :key="item.id" class="ka-ord-block">
                    <div class="ka-ord-line">
                        <strong>{{ locale === 'en' ? item.name_en : item.name_ar }}</strong>
                        <span class="ka-sup-muted">{{ item.base_qty }} {{ unitOf(item) }} · {{ money(item.price_per_unit) }} SAR/{{ unitOf(item) }}</span>
                    </div>
                    <p v-if="item.added || item.selected_key" class="ka-ord-extra">
                        <span v-if="item.added">{{ t.addedFlag }}</span>
                        <span v-if="item.selected_key">{{ item.added ? ' · ' : '' }}{{ t.swapped }} {{ deltaText(item.extra_cost) }}</span>
                    </p>
                    <p v-else-if="Number(item.extra_cost) > 0" class="ka-ord-extra">{{ t.extra }}: +{{ money(item.extra_cost) }} SAR</p>
                </div>
                </template>

                <div v-if="order.status === 'design_preparation'" class="ka-ord-editor">
                    <fieldset :disabled="!can('orders.edit')">
                    <article v-for="(item, index) in drafts" :key="item.id || index" class="ka-comp-card">
                        <div v-if="item.added || item.selected_key" class="ka-comp-flags">
                            <span v-if="item.added" class="ka-comp-flag">{{ t.addedFlag }}</span>
                            <span v-if="item.selected_key" class="ka-comp-flag">{{ t.swapped }} {{ deltaText(item.extra_cost) }}</span>
                        </div>
                        <div class="ka-comp-grid">
                            <label class="ka-field"><span>{{ t.category }}</span>
                                <select :value="item.category" @change="setCategory(item, $event.target.value)">
                                    <option v-for="cat in categories" :key="cat.code" :value="cat.code">{{ locale === 'en' ? cat.en : cat.ar }}</option>
                                </select>
                            </label>
                            <span class="ka-comp-spacer"></span>
                            <label class="ka-field"><span>{{ t.nameAr }}</span><input v-model="item.name_ar"></label>
                            <label class="ka-field"><span>English name</span><input v-model="item.name_en"></label>
                            <label class="ka-field"><span>{{ t.unitAr }}</span><input v-model="item.unit_label_ar"></label>
                            <label class="ka-field"><span>{{ t.unitEn }}</span><input v-model="item.unit_label_en"></label>
                            <label class="ka-field"><span>{{ t.qty }}</span><input v-model.number="item.base_qty" type="number" min="0"></label>
                            <label class="ka-field"><span>{{ t.unitPrice }}</span><input v-model.number="item.price_per_unit" type="number" min="0"></label>
                        </div>
                        <p class="ka-comp-price">{{ t.linePrice }}: {{ money(shownPrice(item)) }} SAR<span v-if="item.selected_key" class="ka-comp-delta">{{ t.swapDelta }} {{ deltaText(item.extra_cost) }}</span></p>
                        <div class="ka-comp-alts">
                            <p class="ka-comp-alts-title">{{ t.altsTitle }} <span class="ka-sup-muted">{{ t.categoryOf }}: {{ categoryName(item.category) }}</span></p>
                            <div v-for="(option, optionIndex) in item.options" :key="optionIndex" class="ka-comp-alt" :class="{ 'is-swapped': item.selected_key && item.selected_key === altKey(option) }">
                                <label class="ka-field"><span>{{ t.altAr }}</span><input v-model="option.name_ar"></label>
                                <label class="ka-field"><span>Alternative</span><input v-model="option.name_en"></label>
                                <label class="ka-field"><span>{{ t.delta }}</span><input v-model.number="option.price_delta" type="number" @input="syncAlt(item, option)"></label>
                                <button v-if="can('orders.edit')" type="button" class="ka-btn ka-btn-outline" @click="swapAlt(item, option)">{{ item.selected_key === altKey(option) ? t.swapped : t.swap }}</button>
                                <button v-if="can('orders.edit')" type="button" class="ka-btn ka-btn-ghost" @click="removeOption(item, optionIndex)">{{ t.remove }}</button>
                            </div>
                            <div v-if="can('orders.edit')" class="ka-ord-actions">
                                <select class="ka-ord-select" @change="insertAlt($event, item)">
                                    <option value="">{{ t.insertAlt }}</option>
                                    <option v-for="alt in altsFor(item.category)" :key="alt.id" :value="alt.id">{{ (locale === 'en' ? alt.name_en : alt.name_ar) + ' (' + deltaText(alt.price_delta) + ')' }}</option>
                                </select>
                                <button type="button" class="ka-btn ka-btn-outline" @click="item.options.push({ name_ar: '', name_en: '', price_delta: 0 })">{{ t.manualAlt }}</button>
                                <button type="button" class="ka-btn ka-btn-ghost" @click="drafts.splice(index, 1)">{{ t.removeComponent }}</button>
                            </div>
                            <p v-if="!altsFor(item.category).length" class="ka-sup-muted">{{ t.noAlts }}</p>
                        </div>
                    </article>
                    <div v-if="can('orders.edit')" class="ka-ord-actions">
                        <button type="button" class="ka-btn ka-btn-outline" @click="loadItems">{{ t.loadItems }}</button>
                        <button v-if="loadedFromPackage" type="button" class="ka-btn ka-btn-outline" @click="undoLoad">{{ t.undoLoad }}</button>
                        <button type="button" class="ka-btn ka-btn-outline" @click="addDraft">{{ t.addComponent }}</button>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="saveComponents">{{ t.saveComponents }}</button>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving || !order.design_ready" @click="sendDesign">{{ t.sendDesign }}</button>
                    </div>
                    <p v-if="order.design_status === 'rejected'" class="ka-ord-hint">{{ t.designRejectedHint }}</p>
                    <p v-if="designRejectNote" class="ka-ord-error">{{ designRejectNote }}</p>
                    <p v-if="!order.design_ready" class="ka-ord-error">{{ t.designNeedFile }}</p>
                    <p v-if="loadedFromPackage" class="ka-ord-hint">{{ t.packageIncluded }}</p>
                    <p v-if="drafts.length" class="ka-comp-sum">{{ t.componentsSum }}: {{ money(editorCharge) }} SAR</p>
                    </fieldset>
                </div>
                </div>

                <div class="ka-ord-panel">
                <h3>{{ t.suppliers }}</h3>
                <p v-if="!order.assignments.length" class="ka-sup-muted">{{ t.none }}</p>
                <div v-for="row in order.assignments" :key="row.id" class="ka-ord-block">
                    <div class="ka-ord-line">
                        <strong>{{ locale === 'en' ? row.supplier_name_en : row.supplier_name_ar }}</strong>
                        <span class="ka-sup-muted">{{ locale === 'en' ? row.scope_en : row.scope_ar }}</span>
                        <span>{{ money(row.cost) }} SAR</span>
                    </div>
                    <p class="ka-sup-muted">{{ t.expected }}: {{ row.expected_date || '—' }}<template v-if="row.delivered_at"> · {{ t.delivered }}: {{ row.delivered_at }}</template></p>
                    <label v-if="can('orders.edit') && order.status === 'in_supply' && !row.delivered_at" class="ka-field ka-ord-date">
                        <span>{{ t.delivered }}</span>
                        <input type="date" @change="markDelivered(row.id, $event.target.value)">
                    </label>
                </div>
                <div v-if="canAssign && can('orders.edit')" class="ka-ord-editor">
                    <article v-for="(row, index) in supplierDrafts" :key="index" class="ka-ord-form">
                        <label class="ka-field"><span>{{ t.supplier }}</span>
                            <select v-model="row.supplier_id">
                                <option value="">{{ t.pickSupplier }}</option>
                                <option v-for="supplier in activeSuppliers" :key="supplier.id" :value="supplier.id">{{ locale === 'en' ? supplier.name_en : supplier.name_ar }}</option>
                            </select>
                        </label>
                        <label class="ka-field"><span>{{ t.scope }}</span><input v-model="row.scope_ar"></label>
                        <label class="ka-field"><span>{{ t.cost }}</span><input v-model.number="row.cost" type="number" min="0"></label>
                        <label class="ka-field"><span>{{ t.expected }}</span><input v-model="row.expected_date" type="date" :min="todayIso"></label>
                    </article>
                    <div class="ka-ord-actions">
                        <button type="button" class="ka-btn ka-btn-outline" @click="supplierDrafts.push({ supplier_id: '', scope_ar: '', cost: 0, expected_date: '' })">{{ t.addSupplier }}</button>
                        <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="assignSuppliers">{{ t.assign }}</button>
                    </div>
                </div>
                </div>
                </div>

                <div class="ka-ord-thread">
                    <h3>{{ t.thread }}</h3>
                    <p class="ka-sup-muted">{{ t.chatHint }}</p>
                    <div class="ka-ord-actions">
                        <button type="button" class="ka-btn ka-btn-primary ka-chat-launch" @click="openChat">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                            {{ t.startChat }}
                            <span v-if="order.unread" class="ka-unread">{{ order.unread }}</span>
                        </button>
                        <p v-if="order.unread" class="ka-unread-note">{{ t.newMessages }}: {{ order.unread }}</p>
                        <button v-if="can('orders.edit') && order.design_status === 'rejected'" type="button" class="ka-btn ka-btn-outline" :disabled="saving || !order.design_ready" @click="sendDesign">{{ t.resubmit }}</button>
                    </div>
                </div>
            </section>

            <section class="ka-ord-card">
                <h2>{{ t.history }}</h2>
                <p v-if="!order.events.length" class="ka-sup-muted">{{ t.noHistory }}</p>
                <div v-else class="ka-ord-events">
                    <article v-for="event in order.events" :key="event.id" class="ka-ord-event">
                        <div class="ka-ord-event-top">
                            <strong>{{ stageName(event.from_status) }} → {{ stageName(event.to_status) }}</strong>
                            <span class="ka-ord-event-meta">{{ event.actor_name || '—' }} · {{ date(event.created_at) }}</span>
                        </div>
                        <p v-if="event.note" class="ka-ord-event-note">{{ event.note }}</p>
                    </article>
                </div>
            </section>

            <section v-if="can('orders.edit')" class="ka-ord-card ka-ord-advance">
                <h2>{{ t.advance }}</h2>
                <template v-if="order.next_status">
                    <label class="ka-field"><span>{{ t.updateNote }}</span><textarea v-model="advanceNote" rows="3"></textarea></label>
                    <button type="button" class="ka-btn ka-btn-primary" :disabled="saving" @click="advance">{{ t.moveTo }} {{ locale === 'en' ? order.next_en : order.next_ar }}</button>
                </template>
                <p v-else-if="order.gate_ar" class="ka-sup-muted ka-ord-gate">{{ locale === 'en' ? order.gate_en : order.gate_ar }}</p>
                <p v-else class="ka-sup-muted ka-ord-gate">{{ order.status === 'deposit_pending' ? t.waitDeposit : t.complete }}</p>
            </section>
        </template>
        <p v-else-if="loading" class="ka-sup-muted">…</p>
        <p v-if="error" class="ka-ord-error">{{ error }}</p>
        <div v-if="chatOpen && order" class="ka-chat-layer" @click.self="chatOpen = false">
            <section class="ka-chat" role="dialog" aria-modal="true" :aria-label="t.thread">
                <header class="ka-chat-head">
                    <span class="ka-chat-avatar" aria-hidden="true">{{ chatInitial }}</span>
                    <div class="min-w-0">
                        <strong>{{ order.customer_name }}</strong>
                        <span v-if="chatSeen" class="ka-unread">{{ chatSeen }}</span>
                        <p>{{ order.number }} · {{ t.chatScope }}</p>
                    </div>
                    <button type="button" class="ka-chat-close" @click="chatOpen = false" :aria-label="t.chatClose">×</button>
                </header>
                <div class="ka-chat-log" ref="chatLog">
                    <p v-if="!order.messages.length" class="ka-chat-empty">{{ t.chatEmpty }}</p>
                    <article v-for="message in order.messages" :key="message.id" class="ka-chat-row" :class="{ 'is-mine': message.role === 'admin' }">
                        <div class="ka-chat-bubble" :class="'is-' + (message.kind || 'text')">
                            <img v-if="message.kind === 'image'" class="ka-chat-photo" :src="message.url" alt="" @pointerdown="pressMedia(message)" @pointerup="releaseMedia" @pointerleave="releaseMedia" @pointercancel="releaseMedia" @click="openPreview(message)">
                            <video v-else-if="message.kind === 'video'" class="ka-chat-video" :src="message.url" controls @pointerdown="pressMedia(message)" @pointerup="releaseMedia" @pointercancel="releaseMedia"></video>
                            <audio v-else-if="message.kind === 'audio'" class="ka-chat-audio" :src="message.url" controls></audio>
                            <a v-else-if="message.kind === 'pdf'" class="ka-chat-filelink" :href="message.url" target="_blank" rel="noopener">{{ message.file_name || mediaCaption(message) }}</a>
                            <p v-if="message.body">{{ message.body }}</p>
                            <p v-else-if="message.kind && message.kind !== 'text' && message.kind !== 'pdf'">{{ mediaCaption(message) }}</p>
                            <time>{{ message.kind === 'audio' && message.duration ? voiceLength(message.duration) + ' · ' : '' }}{{ chatTime(message.created_at) }}</time>
                        </div>
                    </article>
                </div>
                <form v-if="can('orders.edit')" class="ka-chat-form" @submit.prevent="sendMessage">
                    <input ref="chatImage" class="ka-chat-file" type="file" accept="image/*,.jpg,.jpeg,.jfif,.png,.webp,.gif,.bmp,.tif,.tiff,.heic,.heif,.avif" @change="sendPicked($event, 'image')">
                    <input ref="chatVideo" class="ka-chat-file" type="file" accept="video/mp4,video/webm,video/quicktime" @change="sendPicked($event, 'video')">
                    <button type="button" class="ka-chat-tool" :disabled="saving || recording" :aria-label="t.chatImage" @click="$refs.chatImage.click()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </button>
                    <button type="button" class="ka-chat-tool" :disabled="saving || recording" :aria-label="t.chatVideo" @click="$refs.chatVideo.click()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>
                    </button>
                    <div v-if="recording" class="ka-chat-recording">
                        <span class="ka-chat-dot"></span>
                        <strong>{{ voiceLength(recordSeconds) }}</strong>
                        <span>{{ t.releaseToSend }}</span>
                    </div>
                    <input v-else v-model="messageBody" type="text" :placeholder="t.write" :disabled="saving">
                    <button v-if="!recording && messageBody.trim()" type="submit" class="ka-btn ka-btn-primary" :disabled="saving" :aria-label="t.send">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    </button>
                    <button v-else type="button" class="ka-btn ka-btn-primary ka-chat-mic" :class="{ 'is-recording': recording }" :disabled="saving" :aria-label="t.chatVoice" @pointerdown.prevent="beginRecord" @pointerup="finishRecord" @pointercancel="finishRecord(true)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><path d="M12 19v3"/></svg>
                    </button>
                </form>
                <p v-if="can('orders.edit')" class="ka-chat-formats">{{ t.chatFormats }}</p>
                <div v-if="previewUrl" class="ka-chat-preview" @click.self="previewUrl = ''">
                    <button type="button" class="ka-chat-close" @click="previewUrl = ''" :aria-label="t.chatClose">×</button>
                    <img v-if="previewKind === 'image'" :src="previewUrl" alt="">
                    <video v-else :src="previewUrl" controls autoplay></video>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
const stages = [
    { code: "deposit_pending", ar: "بانتظار العربون", en: "Awaiting deposit" },
    { code: "admin_review", ar: "قيد مراجعة الإدارة", en: "Under review" },
    { code: "design_preparation", ar: "قيد إعداد التصميم", en: "Design in preparation" },
    { code: "design_review", ar: "بانتظار مراجعة العميل", en: "Awaiting your review" },
    { code: "final_payment", ar: "بانتظار السداد النهائي", en: "Awaiting final payment" },
    { code: "final_approval", ar: "الموافقة النهائية", en: "Final approval" },
    { code: "in_supply", ar: "قيد التنفيذ والتوريد", en: "In production" },
    { code: "supplied", ar: "تم التوريد", en: "Supplied" },
    { code: "closed", ar: "مغلق", en: "Closed" },
];

const fallbackCategories = [
    { code: "furniture", ar: "الأثاث", en: "Furniture", unitAr: "قطعة", unitEn: "piece" },
    { code: "kitchen", ar: "المطبخ", en: "Kitchen", unitAr: "متر طولي", unitEn: "linear meter" },
    { code: "wardrobe", ar: "الدواليب", en: "Wardrobes", unitAr: "متر طولي", unitEn: "linear meter" },
    { code: "appliances", ar: "الأجهزة", en: "Appliances", unitAr: "جهاز", unitEn: "unit" },
    { code: "tv", ar: "الشاشات والتلفزيون", en: "TV & screens", unitAr: "جهاز", unitEn: "unit" },
    { code: "curtains", ar: "الستائر", en: "Curtains", unitAr: "متر", unitEn: "meter" },
    { code: "accessories", ar: "الإكسسوارات", en: "Accessories", unitAr: "قطعة", unitEn: "piece" },
    { code: "soft", ar: "المفروشات", en: "Soft furnishings", unitAr: "قطعة", unitEn: "piece" },
    { code: "lighting", ar: "الإنارة", en: "Lighting", unitAr: "نقطة", unitEn: "point" },
    { code: "general", ar: "عام", en: "General", unitAr: "قطعة", unitEn: "piece" },
];

const blankComponent = () => ({
    category: "furniture",
    name_ar: "",
    name_en: "",
    unit_label_ar: "قطعة",
    unit_label_en: "unit",
    base_qty: 1,
    price_per_unit: 0,
    extra_cost: 0,
    extra_qty: 0,
    selected_key: "",
    options: [],
});

const ProgressTrack = {
    props: ["percent", "step", "label", "wide"],
    template: `
        <div class="ka-progress" :class="{ 'is-wide': wide }">
            <div class="ka-progress-top"><span>{{ label }}</span><strong>{{ percent }}%</strong></div>
            <div class="ka-track"><div class="ka-track-fill" :style="{ width: percent + '%' }"></div><span v-for="index in 8" :key="index" class="ka-dot" :class="{ 'is-on': index <= step }"></span></div>
        </div>
    `,
};

export default {
    components: { ProgressTrack },
    data() {
        return {
            rows: [],
            order: null,
            query: "",
            stage: "all",
            stages,
            categories: fallbackCategories,
            loading: true,
            saving: false,
            error: "",
            drafts: [],
            editorOpen: false,
            loadedFromPackage: false,
            draftsBeforeLoad: null,
            supplierDrafts: [],
            messageBody: "",
            chatOpen: false,
            chatSeen: 0,
            recording: false,
            recordSeconds: 0,
            previewUrl: "",
            previewKind: "image",
            advanceNote: "",
            paymentNote: "",
            contractOpen: false,
            designQueue: [],
            handoverQueue: [],
            designOver: false,
            handoverOver: false,
        };
    },
    computed: {
        locale() {
            return this.$route.params.locale === "en" ? "en" : "ar";
        },
        orderId() {
            return this.$route.params.id || null;
        },
        t() {
            const en = this.locale === "en";
            return en ? {
                title: "Order management",
                lead: "Review orders and plans, prepare designs and pricing, then track suppliers until closing.",
                search: "Search order, customer or unit",
                allStages: "All stages",
                order: "Order",
                customer: "Customer / unit",
                amount: "Amount",
                stage: "Stage",
                progress: "Progress",
                details: "Details",
                empty: "No orders yet.",
                all: "All orders",
                info: "Order information",
                customerName: "Customer",
                username: "Username",
                project: "Project",
                unit: "Unit",
                bedrooms: "Bedrooms",
                package: "Package",
                tier: "Sub-package",
                created: "Created",
                designStatus: "Design status",
                financials: "Financials",
                excl: "Excl. VAT",
                vat: "VAT",
                packageTotal: "Package total",
                addons: "Add-ons",
                finalTotal: "Final total",
                deposit: "Deposit",
                paidState: "paid",
                unpaid: "unpaid",
                paid: "Paid",
                remaining: "Remaining",
                payments: "Payments",
                noPayments: "No payment yet.",
                paymentSection: "Payment",
                method: "Method",
                payState: "Payment status",
                viewReceipt: "View receipt",
                paymentOk: "Payment confirmed. You can continue the rest of the order.",
                rejectNote: "Rejection note for the customer",
                rejectPlaceholder: "Write why the payment was rejected",
                approvePay: "Confirm payment",
                rejectPay: "Reject payment",
                customerPlan: "Plan uploaded by the customer",
                noPlan: "The customer has not uploaded a plan.",
                confirmFirst: "Confirm the payment before the rest of the workflow.",
                finalWaitCustomer: "Waiting for the customer to pay the final amount.",
                finalReviewTitle: "Final payment review",
                finalPending: "The customer sent the final payment. Confirm or reject it.",
                confirmFinal: "Confirm final payment",
                finalRejectedWait: "Final payment was rejected. Waiting for the customer to pay again.",
                finalOk: "Final payment confirmed.",
                designRejectedHint: "The customer rejected the design. Update the files or components if needed, then resubmit.",
                designNeedFile: "Upload a design file (image or PDF) before sending it to the customer.",
                viewFinalReceipt: "View final receipt",
                workspace: "Design, documents and suppliers",
                designFiles: "Design files and versions",
                handoverFiles: "Handover report",
                handoverRequired: "A handover report is required before this order can be closed.",
                handoverHint: "Required before closing. JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, or PDF, up to 25 MB.",
                otherDocs: "Other order documents",
                contract: "Signed furnishing agreement",
                uploadDesign: "Upload the design (images or PDF)",
                designLimit: "Accepted: JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, and PDF. Several files, up to 25 MB each.",
                dropFiles: "Drop files here or click to browse",
                uploadNow: "Upload selected files",
                chatFormats: "Photos: JPG, PNG, WEBP, GIF, BMP, HEIC, AVIF. Video: MP4, WEBM, MOV.",
                uploadHandover: "Upload handover report (required before closing)",
                components: "Design components and pricing",
                extra: "Customer change",
                linePrice: "Price",
                componentsSum: "Components price",
                swap: "Swap",
                swapped: "Swapped",
                addedFlag: "Added",
                swapDelta: "Price difference",
                category: "Category",
                categoryOf: "Category",
                nameAr: "Arabic name",
                unitAr: "Unit label",
                unitEn: "Unit label (English)",
                qty: "Base quantity",
                unitPrice: "Price per extra unit",
                altsTitle: "Alternatives shown to the customer",
                noAlts: "No active alternatives for this category — add them in the Alternatives page.",
                extraCost: "Extra cost",
                altAr: "Alternative (Arabic)",
                delta: "Price delta",
                remove: "Remove",
                insertAlt: "Insert from library",
                manualAlt: "Manual alternative",
                removeComponent: "Remove component",
                loadItems: "Load sub-package items",
                undoLoad: "Undo load",
                addComponent: "Add component",
                saveComponents: "Save components",
                packageIncluded: "Package item prices are already in the package total. Saving them does not add that amount again — only extras and swapped alternatives are billed.",
                sendDesign: "Send design to customer",
                suppliers: "Suppliers and delivery",
                expected: "Expected",
                delivered: "Delivered",
                supplier: "Supplier",
                pickSupplier: "Select supplier",
                scope: "Scope",
                cost: "Cost",
                addSupplier: "Add supplier",
                assign: "Assign and notify",
                datePast: "The supply date cannot be in the past.",
                thread: "Order chat",
                chatHint: "Each order has its own conversation. Messages stay with this order.",
                startChat: "Start your conversation with the customer",
                newMessages: "New messages",
                chatScope: "This order only",
                chatEmpty: "No messages on this order yet.",
                chatClose: "Close",
                chatImage: "Photo",
                chatVideo: "Video",
                chatVoice: "Hold to record",
                mediaImage: "Photo",
                mediaVideo: "Video",
                mediaAudio: "Voice message",
                mediaPdf: "PDF",
                releaseToSend: "Release to send",
                noMessages: "No messages yet.",
                threadClosed: "Discussion is closed. The log stays available.",
                note: "Note",
                write: "Write your note…",
                send: "Send",
                resubmit: "Resubmit design",
                history: "Stage history",
                noHistory: "No history yet.",
                advance: "Advance stage",
                updateNote: "Update note",
                moveTo: "Move to:",
                waitDeposit: "Waiting for the customer deposit. The stage cannot move before payment.",
                complete: "This order workflow is complete.",
                none: "None yet.",
                admin: "Admin",
            } : {
                title: "إدارة الطلبات",
                lead: "راجع الطلب والمخطط، جهّز التصميم وأسعاره، تابع الموردين والتوريد حتى الإغلاق.",
                search: "ابحث برقم الطلب أو العميل أو الوحدة",
                allStages: "كل المراحل",
                order: "الطلب",
                customer: "العميل / الوحدة",
                amount: "القيمة",
                stage: "المرحلة",
                progress: "تقدم الطلب",
                details: "التفاصيل",
                empty: "لا توجد طلبات بعد.",
                all: "كل الطلبات",
                info: "بيانات الطلب",
                customerName: "العميل",
                username: "اسم المستخدم",
                project: "المشروع",
                unit: "الوحدة",
                bedrooms: "عدد الغرف",
                package: "الباقة",
                tier: "الباقة الفرعية",
                created: "تاريخ الإنشاء",
                designStatus: "حالة التصميم",
                financials: "الملخص المالي",
                excl: "قبل الضريبة",
                vat: "الضريبة",
                packageTotal: "قيمة الباقة",
                addons: "التعديلات",
                finalTotal: "الإجمالي النهائي",
                deposit: "العربون",
                paidState: "مدفوع",
                unpaid: "غير مدفوع",
                paid: "المدفوع",
                remaining: "المتبقي",
                payments: "الإيصالات",
                noPayments: "لا توجد عملية دفع بعد.",
                paymentSection: "الدفع",
                method: "طريقة الدفع",
                payState: "حالة الدفع",
                viewReceipt: "عرض الوصل",
                paymentOk: "تم تأكيد الدفع. يمكنك متابعة باقي الإجراءات.",
                rejectNote: "ملاحظة الرفض وتظهر للعميل",
                rejectPlaceholder: "اكتب سبب رفض الدفع",
                approvePay: "تأكيد الدفع",
                rejectPay: "رفض الدفع",
                customerPlan: "المخطط الذي رفعه العميل",
                noPlan: "لم يرفع العميل مخططاً.",
                confirmFirst: "أكّد الدفع أولاً قبل متابعة باقي الإجراءات.",
                finalWaitCustomer: "بانتظار دفع العميل للمبلغ النهائي.",
                finalReviewTitle: "مراجعة السداد النهائي",
                finalPending: "العميل أرسل السداد النهائي. أكّده أو ارفضه مع ملاحظة.",
                confirmFinal: "تأكيد السداد النهائي",
                finalRejectedWait: "تم رفض السداد النهائي. بانتظار إعادة الدفع من العميل.",
                finalOk: "تم تأكيد السداد النهائي.",
                designRejectedHint: "العميل رفض التصميم. حدّث الملفات أو المكوّنات إن لزم ثم أعد الإرسال.",
                designNeedFile: "ارفع ملف التصميم (صورة أو PDF) قبل إرساله للعميل.",
                viewFinalReceipt: "عرض إيصال السداد النهائي",
                workspace: "التصميم والمستندات والموردون",
                designFiles: "ملفات التصميم ونسخه",
                handoverFiles: "محضر الاستلام",
                handoverRequired: "محضر الاستلام إلزامي قبل إغلاق الطلب.",
                handoverHint: "مطلوب قبل الإغلاق. الصيغ: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF، وحتى 25 ميغابايت.",
                otherDocs: "مستندات الطلب الأخرى",
                contract: "عقد خدمات التأثيث الموقّع",
                uploadDesign: "رفع التصميم (صورة أو PDF)",
                designLimit: "الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF. عدة ملفات، وحتى 25 ميغابايت لكل ملف.",
                dropFiles: "اسحب الملفات هنا أو اضغط للاختيار",
                uploadNow: "رفع الملفات المحددة",
                chatFormats: "الصور: JPG و PNG و WEBP و GIF و BMP و HEIC و AVIF. الفيديو: MP4 و WEBM و MOV.",
                uploadHandover: "رفع محضر الاستلام (إلزامي قبل الإغلاق)",
                components: "مكوّنات التصميم وتسعيرها",
                extra: "تعديل العميل",
                linePrice: "السعر",
                componentsSum: "مجموع تسعير المكوّنات",
                swap: "تبديل",
                swapped: "تم التبديل",
                addedFlag: "تمت الإضافة",
                swapDelta: "فرق السعر",
                category: "التصنيف",
                categoryOf: "التصنيف",
                nameAr: "الاسم بالعربية",
                unitAr: "الوحدة (متر/قطعة)",
                unitEn: "الوحدة بالإنجليزية",
                qty: "الكمية الأساسية",
                unitPrice: "سعر الوحدة الإضافية",
                altsTitle: "البدائل المعروضة للعميل",
                noAlts: "لا توجد بدائل مفعّلة لهذا التصنيف في المكتبة — أضفها من صفحة «البدائل والأسعار».",
                extraCost: "تكلفة إضافية",
                altAr: "بديل بالعربية",
                delta: "فرق السعر",
                remove: "حذف",
                insertAlt: "إدراج بديل من المكتبة",
                manualAlt: "بديل يدوي",
                removeComponent: "حذف المكوّن",
                loadItems: "تحميل عناصر الباقة الفرعية",
                undoLoad: "العودة عن التحميل",
                addComponent: "إضافة مكوّن",
                saveComponents: "حفظ المكوّنات",
                packageIncluded: "أسعار عناصر الباقة داخلة أصلاً في سعر الباقة. حفظها لا يضيف المبلغ مرة ثانية — تُحتسب فقط الإضافات والبدائل المختارة.",
                sendDesign: "إرسال التصميم للعميل",
                suppliers: "الموردون والتوريد",
                expected: "المتوقع",
                delivered: "تاريخ التوريد",
                supplier: "المورد",
                pickSupplier: "اختر المورد",
                scope: "نطاق العمل",
                cost: "التكلفة",
                addSupplier: "إضافة مورد",
                assign: "إسناد وإرسال تنبيه",
                datePast: "تاريخ التوريد لا يمكن أن يكون بتاريخ سابق.",
                thread: "محادثة الطلب",
                chatHint: "لكل طلب محادثة خاصة، وتُحفظ رسائلها مع هذا الطلب فقط.",
                startChat: "ابدأ محادثتك مع العميل",
                newMessages: "رسائل جديدة",
                chatScope: "هذا الطلب فقط",
                chatEmpty: "لا رسائل في هذا الطلب بعد.",
                chatClose: "إغلاق",
                chatImage: "صورة",
                chatVideo: "فيديو",
                chatVoice: "اضغط مطولاً للتسجيل",
                mediaImage: "صورة",
                mediaVideo: "فيديو",
                mediaAudio: "رسالة صوتية",
                mediaPdf: "PDF",
                releaseToSend: "أفلت للإرسال",
                noMessages: "لا توجد رسائل بعد.",
                threadClosed: "أُغلقت المناقشة. السجل يبقى متاحاً للاطلاع.",
                note: "ملاحظة",
                write: "اكتب ملاحظتك على الطلب…",
                send: "إرسال",
                resubmit: "إعادة إرسال التصميم للاعتماد",
                history: "سجل المراحل",
                noHistory: "لا يوجد سجل بعد.",
                advance: "تحديث المرحلة",
                updateNote: "ملاحظة التحديث",
                moveTo: "نقل إلى:",
                waitDeposit: "بانتظار دفع العربون من العميل. لا يمكن نقل المرحلة قبل الدفع.",
                complete: "اكتملت دورة هذا الطلب.",
                none: "لا يوجد بعد.",
                admin: "الإدارة",
            };
        },
        draftPrice() {
            return this.drafts.reduce((sum, item) => sum + this.linePrice(item), 0);
        },
        editorCharge() {
            return this.drafts.reduce((sum, item) => sum + this.charge(item), 0);
        },
        billAddons() {
            if (this.order?.status !== "design_preparation" || !this.editorOpen) {
                return Number(this.order?.addons_total || 0);
            }
            if (this.loadedFromPackage) return this.editorCharge;
            const saved = this.order?.components || [];
            const draftIds = new Set(this.drafts.filter((item) => item.id).map((item) => item.id));
            const hidden = saved.filter((item) => !draftIds.has(item.id)).reduce((sum, item) => sum + this.charge(item), 0);
            return hidden + this.editorCharge;
        },
        billFinal() {
            return Number(this.order?.price_incl || 0) + Number(this.billAddons);
        },
        billRemaining() {
            const paid = this.order?.final_payment_status === "approved"
                ? this.billFinal
                : (this.order?.deposit_paid ? Number(this.order.deposit_amount || 0) : 0);
            return Math.max(0, this.billFinal - paid);
        },
        filtered() {
            const q = this.query.trim().toLowerCase();
            return this.rows.filter((row) => {
                const stageOk = this.stage === "all" || row.status === this.stage;
                const text = `${row.number} ${row.customer_name} ${row.unit_no} ${row.compound_name}`.toLowerCase();
                return stageOk && (!q || text.includes(q));
            });
        },
        plans() {
            return (this.order?.files || []).filter((file) => file.kind === "plan" || !file.kind);
        },
        designFiles() {
            return (this.order?.files || []).filter((file) => file.kind === "design");
        },
        handovers() {
            return (this.order?.files || []).filter((file) => file.kind === "handover");
        },
        otherFiles() {
            return this.plans;
        },
        needsHandover() {
            return this.order && ["in_supply", "supplied"].includes(this.order.status) && !this.handovers.length;
        },
        receipts() {
            return (this.order?.files || []).filter((file) => file.kind === "receipt");
        },
        finalReceipts() {
            return (this.order?.files || []).filter((file) => file.kind === "final_receipt");
        },
        designRejectNote() {
            const event = [...(this.order?.events || [])].reverse().find((row) => String(row.note || "").startsWith("رفض العميل للتصميم"));
            if (!event) return "";
            return String(event.note).replace(/^رفض العميل للتصميم:\s*/, "");
        },
        needsFinalReview() {
            return this.order?.status === "final_payment" && this.order.final_payment_status === "pending";
        },
        canUpload() {
            return this.order && !["deposit_pending", "closed"].includes(this.order.status);
        },
        canOperate() {
            return this.order && this.order.payment_status === "approved";
        },
        latestReceipt() {
            const files = this.receipts;
            return files.length ? files[files.length - 1] : null;
        },
        latestFinalReceipt() {
            const files = this.finalReceipts;
            return files.length ? files[files.length - 1] : null;
        },
        paymentEntries() {
            if (!this.order) return [];
            const rows = [];
            const labelOf = (state) => {
                if (state === "approved") return this.locale === "en" ? "Confirmed" : "تم التأكيد";
                if (state === "rejected") return this.locale === "en" ? "Rejected" : "مرفوض";
                if (state === "pending") return this.locale === "en" ? "Pending" : "بانتظار التأكيد";
                return "—";
            };
            const fileState = (file, files, current) => {
                if (["pending", "approved", "rejected"].includes(file.review_status)) return file.review_status;
                const last = [...files].sort((a, b) => a.id - b.id).at(-1);
                return last && file.id === last.id ? (current || "pending") : "rejected";
            };
            if (this.order.payment_method) {
                if (!this.receipts.length) {
                    rows.push({
                        id: "deposit-meta",
                        label: this.locale === "en" ? "Deposit" : "عربون",
                        ref: this.order.payment_method === "visa" ? "Visa" : this.order.number,
                        amount: this.order.deposit_amount,
                        method: this.methodLabel,
                        state: this.order.payment_status,
                        status: labelOf(this.order.payment_status),
                        at: this.order.created_at,
                        url: null,
                    });
                }
                this.receipts.forEach((file) => {
                    const state = fileState(file, this.receipts, this.order.payment_status);
                    rows.push({
                        id: "receipt-" + file.id,
                        label: this.locale === "en" ? "Deposit" : "عربون",
                        ref: file.name,
                        amount: this.order.deposit_amount,
                        method: this.locale === "en" ? "Receipt" : "رفع إيصال",
                        state,
                        status: labelOf(state),
                        at: file.created_at,
                        url: file.url,
                    });
                });
            }
            if (["pending", "approved", "rejected"].includes(this.order.final_payment_status)) {
                const due = Math.max(0, Number(this.billFinal || 0) - Number(this.order.deposit_amount || 0));
                const finalMethod = this.order.final_payment_method === "receipt"
                    ? (this.locale === "en" ? "Receipt" : "رفع إيصال")
                    : (this.locale === "en" ? "Visa" : "فيزا");
                if (!this.finalReceipts.length) {
                    rows.push({
                        id: "final-meta",
                        label: this.locale === "en" ? "Final payment" : "السداد النهائي",
                        ref: this.order.final_payment_method === "visa" ? "Visa" : "FINAL-" + this.order.number,
                        amount: due,
                        method: finalMethod,
                        state: this.order.final_payment_status,
                        status: labelOf(this.order.final_payment_status),
                        at: this.order.created_at,
                        url: null,
                    });
                }
                this.finalReceipts.forEach((file) => {
                    const state = fileState(file, this.finalReceipts, this.order.final_payment_status);
                    rows.push({
                        id: "final-" + file.id,
                        label: this.locale === "en" ? "Final payment" : "السداد النهائي",
                        ref: file.name,
                        amount: due,
                        method: this.locale === "en" ? "Receipt" : "رفع إيصال",
                        state,
                        status: labelOf(state),
                        at: file.created_at,
                        url: file.url,
                    });
                });
            }
            return rows;
        },
        methodLabel() {
            if (this.order?.payment_method === "visa") return this.locale === "en" ? "Visa" : "فيزا";
            if (this.order?.payment_method === "receipt") return this.locale === "en" ? "Receipt upload" : "رفع إيصال";
            return "—";
        },
        payStateLabel() {
            const map = {
                pending: this.locale === "en" ? "Waiting for confirmation" : "بانتظار التأكيد",
                approved: this.locale === "en" ? "Confirmed" : "تم التأكيد",
                rejected: this.locale === "en" ? "Rejected" : "مرفوض",
            };
            return map[this.order?.payment_status] || "—";
        },
        canAssign() {
            return this.order && ["final_approval", "in_supply"].includes(this.order.status);
        },
        todayIso() {
            const date = new Date();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${date.getFullYear()}-${month}-${day}`;
        },
        activeSuppliers() {
            return (this.order?.suppliers || []).filter((supplier) => supplier.is_active !== false);
        },
        threadClosed() {
            return this.order && (this.order.status === "closed" || this.order.design_status === "approved");
        },
        chatInitial() {
            const name = (this.order?.customer_name || "").trim();
            return name ? name.slice(0, 1) : "؟";
        },
        designLabel() {
            const map = {
                none: this.locale === "en" ? "Not sent" : "لم يُرسل",
                sent: this.locale === "en" ? "Awaiting customer approval" : "بانتظار اعتماد العميل",
                approved: this.locale === "en" ? "Design approved" : "التصميم معتمد",
                rejected: this.locale === "en" ? "Design rejected — awaiting revision" : "التصميم مرفوض — بانتظار التعديل",
            };
            return map[this.order?.design_status] || map.none;
        },
        hint() {
            if (this.order?.design_status === "rejected") {
                return this.locale === "en"
                    ? "The customer rejected the design. Update the files or components if needed, then resubmit."
                    : "العميل رفض التصميم. حدّث الملفات أو المكوّنات إن لزم ثم أعد الإرسال.";
            }
            const ar = {
                deposit_pending: "الطلب محفوظ وبانتظار العربون. تظهر هنا البيانات والمستندات فقط.",
                admin_review: "راجع الطلب والمخطط والإيصال، ثم انقل الطلب لإعداد التصميم.",
                design_preparation: "ارفع نسخة التصميم، حمّل عناصر الباقة، سعّرها ثم أرسلها للعميل.",
                design_review: "التصميم عند العميل. ناقشه هنا، وبعد الاعتماد انقل الطلب للسداد النهائي.",
                final_payment: "بانتظار السداد النهائي من العميل. بعد إرساله يظهر زر التأكيد ثم تنتقل للموافقة النهائية.",
                final_approval: "أسند الموردين ونطاق عمل كل واحد، ثم انقل الطلب للتنفيذ.",
                in_supply: "تابع التوريد، سجّل تاريخ التسليم، وارفع محضر الاستلام.",
                supplied: "تم التوريد. ارفع محضر الاستلام ثم أغلق الطلب.",
                closed: "الطلب مغلق. السجل والملفات تبقى للاطلاع.",
            };
            const en = {
                deposit_pending: "The order is saved and waiting for the deposit. Only its details and documents are shown.",
                admin_review: "Review the order, plan and receipt, then move it to design preparation.",
                design_preparation: "Upload a design version, load the sub-package items, price them, then send them to the customer.",
                design_review: "The design is with the customer. Discuss it here, then move the order to final payment after approval.",
                final_payment: "Waiting for the customer’s final payment. After they send it, confirm it, then move to final approval.",
                final_approval: "Assign suppliers and the scope of each, then move the order into production.",
                in_supply: "Track delivery, record the delivered date, and upload the handover report.",
                supplied: "The order has been supplied. Upload the handover report, then close it.",
                closed: "The order is closed. The log and files stay available.",
            };
            return (this.locale === "en" ? en : ar)[this.order?.status] || "";
        },
    },
    watch: {
        "$route.fullPath"() {
            this.load();
        },
    },
    created() {
        this.load();
        this.noticeTimer = setInterval(() => this.refreshUnread(), 8000);
    },
    beforeUnmount() {
        clearInterval(this.noticeTimer);
    },
    methods: {
        label(item) {
            return this.locale === "en" ? item.en : item.ar;
        },
        stageName(code) {
            const item = stages.find((row) => row.code === code);
            if (!item) return "—";
            return this.locale === "en" ? item.en : item.ar;
        },
        money(value) {
            return Number(value || 0).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        parseStamp(value) {
            if (!value) return null;
            let text = String(value).trim().replace(" ", "T").replace(/\.(\d{3})\d+/, ".$1");
            if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(text) && !/[zZ]|[+-]\d{2}:?\d{2}$/.test(text)) {
                text += "Z";
            }
            const parsed = new Date(text);
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        },
        formatStamp(value, options) {
            const parsed = this.parseStamp(value);
            if (!parsed) return value ? "—" : "";
            return parsed.toLocaleString(this.locale === "en" ? "en-GB" : "ar-SA", {
                hour12: false,
                ...options,
            });
        },
        date(value) {
            if (!value) return "—";
            return this.formatStamp(value, {
                day: "numeric",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            }) || "—";
        },
        chatTime(value) {
            if (!value) return "";
            return this.formatStamp(value, { day: "numeric", month: "short", hour: "2-digit", minute: "2-digit" });
        },
        mediaCaption(message) {
            const labels = { image: this.t.mediaImage, video: this.t.mediaVideo, audio: this.t.mediaAudio, pdf: this.t.mediaPdf };
            return labels[message.kind] || "";
        },
        async openChat() {
            this.chatSeen = Number(this.order?.unread || 0);
            this.chatOpen = true;
            this.scrollChat();
            try {
                const response = await axios.post("KaheelOrders/readMessages", { id: this.order.id });
                this.order.messages = response.data.items.messages;
                this.order.unread = 0;
                this.scrollChat();
            } catch (error) {}
        },
        async refreshUnread() {
            try {
                if (this.order && this.chatOpen) {
                    const response = await axios.post("KaheelOrders/readMessages", { id: this.order.id });
                    this.order.messages = response.data.items.messages;
                    this.order.unread = 0;
                    return;
                }
                const response = await axios.get("KaheelOrders/notices");
                const map = {};
                ((response.data.items || {}).orders || []).forEach((row) => { map[row.id] = row.unread; });
                if (this.order) {
                    this.order.unread = map[this.order.id] || 0;
                } else {
                    this.rows = this.rows.map((row) => ({ ...row, unread: map[row.id] || 0 }));
                }
            } catch (error) {}
        },
        voiceLength(seconds) {
            const total = Math.max(0, Number(seconds) || 0);
            const minutes = Math.floor(total / 60);
            const rest = total % 60;
            return `${minutes}:${String(rest).padStart(2, "0")}`;
        },
        pressMedia(message) {
            clearTimeout(this.mediaTimer);
            this.mediaTimer = setTimeout(() => this.openPreview(message), 450);
        },
        releaseMedia() {
            clearTimeout(this.mediaTimer);
        },
        openPreview(message) {
            if (!message?.url || !["image", "video"].includes(message.kind)) return;
            this.previewKind = message.kind;
            this.previewUrl = message.url;
        },
        async sendPicked(event, kind) {
            const file = event.target.files?.[0];
            event.target.value = "";
            if (file) await this.sendMedia(file, kind);
        },
        async beginRecord(event) {
            if (this.saving || this.recording) return;
            this.holdRecording = true;
            this.recordStartX = event.clientX;
            this.cancelRecord = false;
            try {
                event.currentTarget.setPointerCapture(event.pointerId);
            } catch (error) {}
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true }).catch(() => null);
            if (!this.holdRecording) {
                stream?.getTracks().forEach((track) => track.stop());
                return;
            }
            if (!stream) {
                this.error = this.locale === "en" ? "Allow the microphone to record a voice message." : "اسمح للميكروفون لتسجيل رسالة صوتية.";
                return;
            }
            const mime = MediaRecorder.isTypeSupported("audio/webm") ? "audio/webm" : "audio/mp4";
            this.recordChunks = [];
            this.recordMime = mime;
            this.recordStream = stream;
            this.recordStartedAt = Date.now();
            this.recordSeconds = 0;
            this.recorder = new MediaRecorder(stream, { mimeType: mime });
            this.recorder.ondataavailable = (item) => {
                if (item.data && item.data.size) this.recordChunks.push(item.data);
            };
            this.recorder.start();
            this.recording = true;
            this.recordClock = setInterval(() => {
                this.recordSeconds = Math.floor((Date.now() - this.recordStartedAt) / 1000);
            }, 200);
        },
        finishRecord(event) {
            this.holdRecording = false;
            if (!this.recording) return;
            const moved = event && event.clientX != null && Math.abs(event.clientX - this.recordStartX) > 90;
            const cancelled = event === true || moved;
            const seconds = Math.floor((Date.now() - this.recordStartedAt) / 1000);
            this.stopRecorder(cancelled || seconds < 1, seconds);
        },
        stopRecorder(cancelled, seconds) {
            clearInterval(this.recordClock);
            this.recording = false;
            this.recordSeconds = 0;
            const recorder = this.recorder;
            const chunks = this.recordChunks || [];
            const mime = this.recordMime || "audio/webm";
            const stream = this.recordStream;
            this.recorder = null;
            if (!recorder) {
                stream?.getTracks().forEach((track) => track.stop());
                return;
            }
            recorder.onstop = async () => {
                stream?.getTracks().forEach((track) => track.stop());
                if (cancelled || !chunks.length) return;
                const extension = mime.includes("mp4") ? "m4a" : "webm";
                const file = new File(chunks, `voice.${extension}`, { type: mime });
                await this.sendMedia(file, "audio", seconds);
            };
            if (recorder.state !== "inactive") recorder.stop();
            else stream?.getTracks().forEach((track) => track.stop());
        },
        async sendMedia(file, kind, duration = 0) {
            if (file.size > 25 * 1024 * 1024) {
                this.error = this.locale === "en" ? "Each file must be 25 MB or less." : "كل ملف يجب أن يكون 25 ميغابايت أو أقل.";
                return;
            }
            this.saving = true;
            this.error = "";
            try {
                const body = new FormData();
                body.append("id", this.order.id);
                body.append("kind", kind);
                body.append("file", file);
                if (duration) body.append("duration", String(duration));
                const response = await axios.post("KaheelOrders/message", body);
                if (response.data.status === false) {
                    this.error = response.data.message;
                    return;
                }
                this.apply(response);
                this.scrollChat();
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "The file was not sent." : "لم تُرسل الرسالة.");
            } finally {
                this.saving = false;
            }
        },
        scrollChat() {
            this.$nextTick(() => {
                const log = this.$refs.chatLog;
                if (log) log.scrollTop = log.scrollHeight;
            });
        },
        open(id) {
            this.$router.push(`/${this.locale}/dashboard/orders/${id}`);
        },
        back() {
            this.$router.push(`/${this.locale}/dashboard/orders`);
        },
        async load() {
            this.loading = true;
            this.error = "";
            try {
                if (this.orderId) {
                    const response = await axios.get("KaheelOrders/show", { params: { id: this.orderId } });
                    this.order = response.data.items;
                    this.chatOpen = false;
                    const visible = this.visibleDrafts(this.order.components);
                    this.drafts = visible;
                    this.editorOpen = visible.length > 0;
                    this.loadedFromPackage = false;
                    this.draftsBeforeLoad = null;
                    this.supplierDrafts = [];
                    if (String(this.$route.query.chat || "") === "1") this.openChat();
                } else {
                    const response = await axios.get("KaheelOrders/getAllItems");
                    this.rows = response.data.items || [];
                    this.order = null;
                }
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "Could not load orders." : "تعذر تحميل الطلبات.");
            } finally {
                this.loading = false;
            }
        },
        apply(response) {
            this.order = response.data.items;
            if (this.order?.categories?.length) this.categories = this.order.categories;
        },
        async post(url, payload) {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.post(url, payload);
                if (response.data.status === false) {
                    this.error = response.data.message;
                    return false;
                }
                this.apply(response);
                return true;
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "The action failed." : "تعذر تنفيذ الإجراء.");
            } finally {
                this.saving = false;
            }
        },
        isPdf(file) {
            const mime = String(file?.mime || "").toLowerCase();
            const name = String(file?.name || "").toLowerCase();
            return mime.includes("pdf") || name.endsWith(".pdf");
        },
        isImage(file) {
            const mime = String(file?.mime || "").toLowerCase();
            const name = String(file?.name || "").toLowerCase();
            return (mime.startsWith("image/") && !mime.includes("svg")) || /\.(jpe?g|jfif|png|gif|webp|bmp|tiff?|heic|heif|avif)$/.test(name);
        },
        thumbStyle(file) {
            return this.isImage(file) ? { backgroundImage: `url("${file.url}")` } : null;
        },
        reviewPayment(decision) {
            this.post("KaheelOrders/reviewPayment", {
                id: this.order.id,
                decision,
                note: decision === "rejected" ? this.paymentNote.trim() : "",
            });
            if (decision === "rejected") this.paymentNote = "";
        },
        confirmFinal(decision) {
            this.post("KaheelOrders/confirmFinal", {
                id: this.order.id,
                decision: decision || "approved",
                note: decision === "rejected" ? this.paymentNote.trim() : "",
            });
            if (decision === "rejected") this.paymentNote = "";
        },
        advance() {
            this.post("KaheelOrders/advance", { id: this.order.id, to_status: this.order.next_status, note: this.advanceNote });
            this.advanceNote = "";
        },
        async loadItems() {
            this.saving = true;
            this.error = "";
            try {
                const response = await axios.get("KaheelOrders/packageItems", { params: { id: this.order.id } });
                const items = response.data.items || [];
                if (!items.length) {
                    this.error = this.locale === "en" ? "No items defined in this order's sub-package." : "لا توجد عناصر معرّفة في الباقة الفرعية لهذا الطلب.";
                    return;
                }
                if (!this.loadedFromPackage) {
                    this.draftsBeforeLoad = {
                        editorOpen: this.editorOpen,
                        drafts: this.cloneDrafts(this.drafts),
                    };
                }
                const kept = this.cloneDrafts(this.drafts.filter((item) => item.added));
                this.editorOpen = true;
                this.loadedFromPackage = true;
                this.drafts = [
                    ...items.map((item) => ({
                        ...blankComponent(),
                        ...item,
                        options: item.allow_alternatives ? this.altsFor(item.category).map((alt) => ({ name_ar: alt.name_ar, name_en: alt.name_en, price_delta: Number(alt.price_delta) })) : [],
                    })),
                    ...kept,
                ];
            } catch (error) {
                this.error = error.response?.data?.message || "";
            } finally {
                this.saving = false;
            }
        },
        cloneDrafts(rows) {
            return (rows || []).map((item) => ({
                ...item,
                options: item.options ? item.options.map((option) => ({ ...option })) : [],
            }));
        },
        undoLoad() {
            const previous = this.draftsBeforeLoad;
            this.loadedFromPackage = false;
            this.draftsBeforeLoad = null;
            this.drafts = previous ? this.cloneDrafts(previous.drafts) : [];
            this.editorOpen = Boolean(previous && previous.editorOpen);
        },
        visibleDrafts(components) {
            return this.cloneDrafts((components || []).filter((item) => item.added || item.selected_key));
        },
        addDraft() {
            this.editorOpen = true;
            this.drafts.push({ ...blankComponent(), added: true });
        },
        unitOf(item) {
            return this.locale === "en" ? item.unit_label_en : item.unit_label_ar;
        },
        categoryName(code) {
            const item = this.categories.find((row) => row.code === code);
            if (!item) return code || "—";
            return this.locale === "en" ? item.en : item.ar;
        },
        setCategory(item, code) {
            if (item.category !== code) {
                item.options = [];
                item.selected_key = "";
                item.extra_cost = 0;
            }
            item.category = code;
            const cat = this.categories.find((row) => row.code === code);
            if (!cat) return;
            item.unit_label_ar = cat.unitAr || "قطعة";
            item.unit_label_en = cat.unitEn || "piece";
        },
        deltaText(value) {
            const amount = Number(value || 0);
            const text = this.money(amount);
            return amount > 0 ? `+${text}` : text;
        },
        altsFor(category) {
            return (this.order?.alternatives || []).filter((alt) => alt.category === category);
        },
        insertAlt(event, item) {
            const alt = (this.order.alternatives || []).find((row) => String(row.id) === event.target.value);
            event.target.value = "";
            if (!alt || alt.category !== item.category) return;
            item.options.push({ name_ar: alt.name_ar, name_en: alt.name_en, price_delta: Number(alt.price_delta) });
        },
        linePrice(item) {
            return Number(item.base_qty || 0) * Number(item.price_per_unit || 0) + Number(item.extra_cost || 0);
        },
        charge(item) {
            const unit = Number(item.price_per_unit || 0);
            const delta = item.selected_key ? Number(item.extra_cost || 0) : 0;
            if (item.added) return Number(item.base_qty || 0) * unit + delta;
            return Math.max(0, Number(item.extra_qty || 0)) * unit + delta;
        },
        shownPrice(item) {
            if (item.added) return this.charge(item);
            const included = Number(item.base_qty || 0) * Number(item.price_per_unit || 0);
            return included + this.charge(item);
        },
        componentsPayload() {
            if (this.loadedFromPackage) {
                return this.drafts.map((item) => ({
                    ...item,
                    added: !!item.added,
                    priced: false,
                    extra_qty: Number(item.extra_qty || 0),
                }));
            }
            const map = new Map();
            (this.order.components || []).forEach((item) => {
                if (item.id) map.set(item.id, { ...item, options: item.options ? item.options.map((option) => ({ ...option })) : [] });
            });
            this.drafts.forEach((item, index) => {
                const next = {
                    ...item,
                    added: item.id ? !!item.added : true,
                    priced: false,
                    extra_qty: Number(item.extra_qty || 0),
                    options: item.options ? item.options.map((option) => ({ ...option })) : [],
                };
                if (!item.id) {
                    map.set(`new-${index}-${item.name_ar}`, next);
                    return;
                }
                map.set(item.id, { ...(map.get(item.id) || {}), ...next });
            });
            (this.order.components || []).forEach((item) => {
                if (item.id && (item.added || item.selected_key) && !this.drafts.some((draft) => draft.id === item.id)) {
                    map.delete(item.id);
                }
            });
            return [...map.values()];
        },
        altKey(option) {
            return `${option.name_ar}|${option.name_en}`;
        },
        swapAlt(item, option) {
            const key = this.altKey(option);
            if (item.selected_key === key) {
                item.selected_key = "";
                item.extra_cost = 0;
                return;
            }
            item.selected_key = key;
            item.extra_cost = Number(option.price_delta || 0);
        },
        syncAlt(item, option) {
            if (item.selected_key === this.altKey(option)) item.extra_cost = Number(option.price_delta || 0);
        },
        removeOption(item, index) {
            const option = item.options[index];
            if (option && item.selected_key === this.altKey(option)) {
                item.selected_key = "";
                item.extra_cost = 0;
            }
            item.options.splice(index, 1);
        },
        async saveComponents() {
            const wasLoaded = this.loadedFromPackage;
            const saved = await this.post("KaheelOrders/saveComponents", { id: this.order.id, components: this.componentsPayload() });
            if (!saved) return;
            this.loadedFromPackage = false;
            this.draftsBeforeLoad = null;
            const rows = this.cloneDrafts(this.order.components || []);
            this.drafts = wasLoaded ? rows : rows.filter((item) => item.added || item.selected_key);
            this.editorOpen = this.drafts.length > 0;
        },
        sendDesign() {
            this.post("KaheelOrders/sendDesign", { id: this.order.id });
        },
        assignSuppliers() {
            const rows = this.supplierDrafts.filter((row) => row.supplier_id && row.scope_ar);
            if (!rows.length) return;
            if (rows.some((row) => row.expected_date && row.expected_date < this.todayIso)) {
                this.error = this.t.datePast;
                return;
            }
            this.post("KaheelOrders/assignSuppliers", { id: this.order.id, assignments: rows });
        },
        markDelivered(id, date) {
            if (date) this.post("KaheelOrders/markDelivered", { id: this.order.id, assignment_id: id, delivered_at: date });
        },
        async sendMessage() {
            const body = this.messageBody.trim();
            if (!body) return;
            this.messageBody = "";
            await this.post("KaheelOrders/message", { id: this.order.id, body });
            this.scrollChat();
        },
        onDrop(event, kind) {
            this.designOver = false;
            this.handoverOver = false;
            this.queueFiles({ target: { files: event.dataTransfer?.files || [], value: "" } }, kind);
        },
        queueFiles(event, kind) {
            const selected = Array.from(event.target.files || []).slice(0, 10);
            event.target.value = "";
            const max = 25 * 1024 * 1024;
            const allowedDoc = (file) => {
                const name = file.name || "";
                const type = file.type || "";
                if (/\.(jpe?g|jfif|png|webp|gif|bmp|tiff?|heic|heif|avif|pdf)$/i.test(name)) return true;
                if (type.startsWith("image/") && type !== "image/svg+xml") return true;
                return type === "application/pdf" || type === "application/x-pdf";
            };
            if (selected.some((file) => file.size > max)) {
                this.error = this.locale === "en" ? "Each file must be 25 MB or less." : "كل ملف يجب أن يكون 25 ميغابايت أو أقل.";
                return;
            }
            if (selected.some((file) => !allowedDoc(file))) {
                this.error = this.locale === "en"
                    ? "Accepted formats: JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, and PDF."
                    : "الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF.";
                return;
            }
            const key = kind === "handover" ? "handoverQueue" : "designQueue";
            const extra = selected.map((file) => {
                const pdf = /pdf/i.test(file.type) || /\.pdf$/i.test(file.name);
                return {
                    id: file.name + ":" + file.size + ":" + file.lastModified + ":" + Math.random(),
                    file,
                    pdf,
                    preview: !pdf && file.type.startsWith("image/") ? URL.createObjectURL(file) : "",
                    size: (file.size / (1024 * 1024)).toFixed(2) + " MB",
                };
            });
            this[key] = this[key].concat(extra).slice(0, 10);
        },
        removeQueued(kind, index) {
            const key = kind === "handover" ? "handoverQueue" : "designQueue";
            const item = this[key][index];
            if (item?.preview) URL.revokeObjectURL(item.preview);
            this[key] = this[key].filter((_, i) => i !== index);
        },
        async uploadQueued(kind) {
            const key = kind === "handover" ? "handoverQueue" : "designQueue";
            const selected = this[key].map((item) => item.file);
            if (!selected.length) return;
            this.saving = true;
            this.error = "";
            try {
                for (const file of selected) {
                    const body = new FormData();
                    body.append("id", this.order.id);
                    body.append("kind", kind);
                    body.append("file", file);
                    const response = await axios.post("KaheelOrders/upload", body);
                    if (response.data.status === false) {
                        this.error = response.data.message;
                        return;
                    }
                    this.apply(response);
                }
                this[key].forEach((item) => item.preview && URL.revokeObjectURL(item.preview));
                this[key] = [];
            } catch (error) {
                this.error = error.response?.data?.message || (this.locale === "en" ? "The file was not uploaded." : "لم يُرفع الملف.");
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>
