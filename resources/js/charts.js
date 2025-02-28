import Chart from "chart.js/auto";

const ctx1 = document.getElementById("weeklyChart").getContext("2d");
new Chart(ctx1, {
    type: "bar",
    data: {
        labels: ["Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri"],
        datasets: [
            {
                label: "Deposit",
                data: [100, 300, 200, 400, 500, 300, 400],
                backgroundColor: "blue",
            },
            {
                label: "Withdraw",
                data: [50, 200, 150, 300, 250, 100, 200],
                backgroundColor: "cyan",
            },
        ],
    },
});

const ctx2 = document.getElementById("expenseChart").getContext("2d");
new Chart(ctx2, {
    type: "pie",
    data: {
        labels: ["Entertainment", "Bill Expense", "Investment", "Others"],
        datasets: [
            {
                data: [30, 15, 20, 35],
                backgroundColor: ["#FF5733", "#FFBD33", "#33FF57", "#3357FF"],
            },
        ],
    },
});
