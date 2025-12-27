/**
 * action တွေကို permission နဲ့ ကာချင်ရင် သုံးဖို့
 * permission_slug.action (eg, dashboard-view.create, user-role.edit)
 */
export const checkPermissions = (permission) => {
    // Always get the latest from localStorage
    const userRole = JSON.parse(localStorage.getItem("role"));
    const userPermissions = JSON.parse(localStorage.getItem("permission"));
    const superRoles = ["super-admin", "tech-admin", "admin"];

    if (userRole && superRoles.includes(userRole.slug)) {
        return true;
    }

    if (permission === "undefined" || !permission) return false;

    let splitVal = permission.split(".");
    if (splitVal && splitVal.length < 2) return false;

    if (userPermissions && userPermissions.length > 0) {
        let targetPermission = userPermissions.find(
            (p) => p.slug == splitVal[0]
        );
        if (targetPermission && targetPermission[splitVal[1]]) {
            return true;
        }
        return false;
    }
    return false;
};

export const hasAnyPermission = (resource) => {
    const userRole = JSON.parse(localStorage.getItem("role"));
    const userPermissions = JSON.parse(localStorage.getItem("permission"));
    const superRoles = ["super-admin", "tech-admin", "admin"];

    if (userRole && superRoles.includes(userRole.slug)) {
        return true;
    }

    if (!resource || !userPermissions) return false;

    let target = userPermissions.find((p) => p.slug === resource);
    
    if (!target) return false;

    // check if user has at least one true permission
    return ["create", "detail", "update", "edit", "delete"].some(
        (action) => target[action] === true
    );
};

export const setLocalStorage = (response) => {
    const token = response.data.token;
    const user = response.data.user;
    const permissions = response.data.permissions;
    const role = response.data.role;

    localStorage.setItem("token", token); // Store the token in localStorage
    localStorage.setItem("user", JSON.stringify(user)); // Store the user data in localStorage
    localStorage.setItem("role", JSON.stringify(role));
    localStorage.setItem("permission", JSON.stringify(permissions));
};

export const clearLocalStorage = () => {
    localStorage.removeItem("user");
    localStorage.removeItem("token");
    localStorage.removeItem("permission");
    localStorage.removeItem("role");
};

export const timeAgo = (dateString) => {
  const now = new Date();
    const past = new Date(dateString);
    console.log('raw date string', dateString)
    console.log('past here', past);
  const diffInSeconds = Math.floor((now - past) / 1000);

  if (diffInSeconds < 60) {
    return "just now";
  }

  const minutes = Math.floor(diffInSeconds / 60);
  if (minutes < 60) {
    return minutes === 1 ? "1 min ago" : `${minutes} min ago`;
  }

  const hours = Math.floor(minutes / 60);
  if (hours < 24) {
    return hours === 1 ? "1 hour ago" : `${hours} hours ago`;
  }

  const days = Math.floor(hours / 24);
  if (days < 30) {
    return days === 1 ? "1 day ago" : `${days} days ago`;
  }

  const months = Math.floor(days / 30);
  if (months < 12) {
    return months === 1 ? "1 month ago" : `${months} months ago`;
  }

  const years = Math.floor(months / 12);
  return years === 1 ? "1 year ago" : `${years} years ago`;
}

export const formatStatus = (status) => {
    if (!status) return '';

    return status.split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ")
}
