const statusSelector = document.getElementById("status");
const formatGroup = document.getElementById("format");
const inPersonGroup = document.getElementById("inPerson");
const onlineGroup = document.getElementById("online");

if (statusSelector.value !== "Interviewing") {
    formatGroup.disabled = true;
    inPersonGroup.disabled = true;
    onlineGroup.disabled = true;
}

statusSelector.addEventListener("change", function (event) {
    if (statusSelector.value === "Interviewing") {
        formatGroup.disabled = false;
        if (document.getElementById("inPersonSelector").checked) {
            inPersonGroup.disabled = false;
            onlineGroup.disabled = true;
        } else if (document.getElementById("onlineSelector").checked) {
            inPersonGroup.disabled = true;
            onlineGroup.disabled = false;
        }
    } else {
        formatGroup.disabled = true;
        inPersonGroup.disabled = true;
        onlineGroup.disabled = true;
    }
});

document.getElementById("addInPersonDate").addEventListener("click", function (event) {
    event.preventDefault();
    const container = document.createElement("div");

    const startLabel = document.createElement("label");
    startLabel.innerText = "Interview start: ";
    const startInput = document.createElement("input");
    startInput.type = "datetime-local";
    startInput.name = "inPersonStart[]";
    startLabel.appendChild(startInput);

    const endLabel = document.createElement("label");
    endLabel.innerText = "Interview end: ";
    const endInput = document.createElement("input");
    endInput.type = "datetime-local";
    endInput.name = "inPersonEnd[]";
    endLabel.appendChild(endInput);

    const removeButton = document.createElement("button");
    removeButton.classList.add("removeDate");
    removeButton.innerText = "Remove";
    removeButton.addEventListener("click", function (event) {
        event.preventDefault();
        event.target.parentNode.remove();
    });

    container.appendChild(startLabel);
    container.appendChild(endLabel);
    container.appendChild(removeButton);

    const parentNode = event.target.parentNode;

    parentNode.insertBefore(container, event.target);
});

const removeButtons = document.getElementsByClassName("removeDate");
for (let i = 0; i < removeButtons.length; i++) {
    removeButtons.item(i).addEventListener("click", function (event) {
        event.preventDefault();
        event.target.parentNode.remove();
    });
}

document.getElementById("inPersonSelector").addEventListener("click", function (event) {
    document.getElementById("inPerson").disabled = false;
    document.getElementById("online").disabled = true;
});

if (document.getElementById("inPersonSelector").checked && !formatGroup.disabled) {
    document.getElementById("inPerson").disabled = false;
    document.getElementById("online").disabled = true;
}

document.getElementById("onlineSelector").addEventListener("click", function (event) {
    document.getElementById("inPerson").disabled = true;
    document.getElementById("online").disabled = false;
});

if (document.getElementById("onlineSelector").checked && !formatGroup.disabled) {
    document.getElementById("inPerson").disabled = true;
    document.getElementById("online").disabled = false;
}