import DatePicker from "@/components/common/DatePicker.vue";
import InputBox from "@/components/common/InputBox.vue";
import PageHeader from "@/components/common/PageHeader.vue";
import SelectBox from "@/components/common/SelectBox.vue";
import Textarea from "@/components/common/Textarea.vue";
import SpinLoading from "@/components/loading/SpinLoading.vue";
import BarScale from "@/components/loading/BarScale.vue";
import SmallButton from "@/components/common/SmallButton.vue";
import DotLoading from "@/components/loading/DotLoading.vue";
import Footable from "@/components/common/Footable.vue";
import CheckBox from "@/components/common/CheckBox.vue";
import Model from "@/components/Model.vue";
import FileUpload from "@/components/common/FileUpload.vue";
import CommentCard from "./components/CommentCard.vue";
import Comment from "./components/Comment.vue"

export function registerComponents(app) {
    app.component("DatePicker", DatePicker);
    app.component("InputBox", InputBox);
    app.component("PageHeader", PageHeader);
    app.component("SelectBox", SelectBox);
    app.component("Textarea", Textarea);
    app.component("SpinLoading", SpinLoading);
    app.component("BarScaleLoading", BarScale);
    app.component("SmallButton", SmallButton);
    app.component("DotLoading", DotLoading);
    app.component("FooTable", Footable);
    app.component("CheckBox", CheckBox);
    app.component("Model", Model);
    app.component("FileUpload", FileUpload);
    app.component("Comment", Comment);
    app.component("CommentCard", CommentCard);

}
