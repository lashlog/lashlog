<!-- CustomerCreateModal.vue -->
<template>
    <Dialog v-model="isOpen">
        <DialogHeader>
            <DialogTitle>顧客を登録</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="submit">
            <div class="space-y-4">
                <LabeledInput label="氏名" v-model="form.name" required />
                <LabeledInput label="電話番号" v-model="form.phone" required />
                <LabeledInput
                    label="生年月日"
                    v-model="form.birthday"
                    type="date"
                />
                <LabeledTextArea label="メモ" v-model="form.memo" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <Button type="button" variant="outline" @click="close"
                    >キャンセル</Button
                >
                <Button type="submit" :disabled="loading">登録</Button>
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref } from "vue";
import Dialog from "../../../components/ui/dialog/Dialog.vue";
import DialogHeader from "../../../components/ui/dialog/DialogHeader.vue";
import DialogTitle from "../../../components/ui/dialog/DialogTitle.vue";
import Button from "../../../components/ui/button/Button.vue";
import LabeledInput from "../../../components/ui/LabeledInput.vue";
import LabeledTextArea from "../../../components/ui/LabeledTextArea.vue";
import axios from "axios";

// 外部から制御される open 状態
const isOpen = ref(false);
const emit = defineEmits(["created"]);

// 顧客情報
const form = ref({
    name: "",
    phone: "",
    birthday: "",
    memo: "",
});
const loading = ref(false);

// モーダル開閉メソッドを外部公開
const open = () => (isOpen.value = true);
const close = () => {
    isOpen.value = false;
    form.value = {
        name: "",
        phone: "",
        birthday: "",
        memo: "",
    };
};
defineExpose({ open });

// 登録処理
const submit = async () => {
    loading.value = true;
    try {
        const response = await axios.post("/api/shop/customers", form.value);
        emit("created", response.data);
        close();
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};
</script>
